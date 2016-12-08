<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\CoreBundle\Entity\Task;
use Sgpc\CoreBundle\Form\TaskType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class TaskController extends Controller {

    /**
     * Crea una nueva entidad Task
     */
    public function createAction(Request $request, $id) {
        $entity = new Task();
        $parent = $this->getDoctrine()->getRepository('SgpcCoreBundle:Listing')->findOneById($id);
        $project = $parent->getProject();
        $form = $this->createForm(new TaskType(), $entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setListing($parent);
            $entity->setProject($project);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('sgpc_task_view', array('id' => $entity->getId())));
        }

        return $this->render('SgpcCoreBundle:Task:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Display del form para crear una nueva entidad Task
     */
    public function addAction($id) {
        $entity = new Task();

        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('sgpc_task_create', array('id' => $id)),
        ));

        return $this->render('SgpcCoreBundle:Task:add.html.twig', array(
                    'entity' => $entity,
                    'create_form' => $form->createView(),
        ));
    }

    /**
     * Muestra la entidad Task
     */
    public function viewAction($id) {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }

        $comments = $em->getRepository('SgpcCoreBundle:Comment')
                ->getCommentsForTask($task->getId());

        $storeForm = $this->createStoreForm($id);


        return $this->render('SgpcCoreBundle:Task:view.html.twig', array(
                    'task' => $task,
                    'comments' => $comments,
                    'store_form' => $storeForm->createView()
        ));
    }

    /*
     * Crea el formulario para añadir un miembro a la tarea, comprobando que solo
     * se puedan seleccionar los miembros del proyecto del cual depende la tarea
     */

    private function createAddMemberForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_task_addmember', array('id' => $id)))
                        ->setMethod('POST')
                        ->add('user', 'choice', array(
                            'choices' => $this->arrayToChoices($id),
                        ))
                        ->add('submit', 'submit', array('label' => 'Añadir usuario'))
                        ->getForm();
    }

    /**
     * Función para pasar los usuarios miembros de un proyecto como array 
     * al formulario de createAddMemberForm($id)
     */
    protected function arrayToChoices($id) {

        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);
        $list = $task->getListing();
        $project = $list->getProject()->getId();

        /*
         * ToDo: poner en repository. Seleccionamos los usuarios que son miembros
         * del proyecto y no son miembros de esta tarea para pasarlos al form como choices
         */

        $query = $em->createQuery('SELECT u FROM SgpcCoreBundle:User u JOIN u.projects p WHERE  p.id = :idProject AND u.id NOT IN(SELECT u2 FROM SgpcCoreBundle:User u2 JOIN u2.tasks t WHERE t.id= :idTask)');
        $query->setParameters(array(
            'idTask' => $id,
            'idProject' => $project,
        ));
        $projectMembers = $query->getResult();

        foreach ($projectMembers as $projectMember) {
            $choices[$projectMember->getId()] = $projectMember->getUsername();
        }
        if (empty($choices)) {
            return array('Todos son miembros');
        }
        return $choices;
    }

    /**
     * Añade un miembro a la tarea
     */
    public function addMemberAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createAddMemberForm($id);
        $form->handleRequest($request);

        $task = $em->getRepository('SgpcCoreBundle:Task')->findOneById($id);

        if ($form->isValid()) {

            $formuserId = $form->get('user')->getData();

            $user = $em->getRepository('SgpcCoreBundle:User')->findOneBy(array('id' => $formuserId));
            if (!$user) {
                throw $this->createNotFoundException('No se ha encontrado el usuario.');
            }
            $task->addUser($user);
            $em->persist($task);
            $em->persist($user);
            $em->flush();

            return $this->redirect($this->generateUrl('sgpc_task_view', array('id' => $task->getId())));
        }

        return $this->render('SgpcCoreBundle:Task:addmember.html.twig', array(
                    'task' => $task,
                    'addmember_form' => $form->createView(),
        ));
    }

    /* crea el formulario para archivar una tarea desde el desplegable */

    private function createStoreForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_task_store', array('id' => $id)))
                        ->setMethod('POST')
                        ->add('submit', 'submit', array('label' => 'Archivar tarea', 'attr' => array(
                                'class' => 'btn btn-danger btn-sm',
                                'onclick' => 'return confirm("Estás seguro que quieres archivar la tarea?")'
                    )))
                        ->getForm();
    }

    /* Accion que se encarga de archivar una tarea */

    public function storeAction(Request $request, $id) {
        $form = $this->createStoreForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);
            if (!$task) {
                throw $this->createNotFoundException('No se ha encontrado la entidad tarea.');
            }

            $task->setIsActive(false);
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('sgpc_task_view', array('id' => $task->getId())));
        }
    }

    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('task not found');
        }

        $form = $this->createEditForm($task);

        return $this->render('SgpcCoreBundle:Task:edit.html.twig', array('task' => $task, 'edit_form' => $form->createView()));
    }

    private function createEditForm(Task $entity) {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('sgpc_task_update', array('id' => $entity->getId())),
            'method' => 'PUT'
        ));
        $form   ->add('listing') //FALTA HACER QUE SOLO PUEDA SELECCIONAR LOS DE SU PROYECTO
                ->add('submit', 'submit', array('label' => 'Actualizar tarea', 'attr' => ['class' => 'btn btn-success btn-sm']));

        return $form;
    }

    public function updateAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('task not found');
        }

        $form = $this->createEditForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('sgpc_task_view', array('id' => $task->getId()));
        }

        return $this->render('SgpcCoreBundle:Task:edit.html.twig', array('task' => $task, 'edit_form' => $form->createView()));
    }

}
