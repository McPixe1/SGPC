<?php

namespace Sgpc\CoreBundle\Controller;

use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Entity\Listing;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use function dump;

class ProjectController extends Controller {
    /*
     * Muestra el listado de proyectos del usuario si esta logeado,
     * en caso contrario lo redirige al registro
     */

    public function indexAction() {
        $securityContext = $this->container->get('security.authorization_checker');

        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            $currentUser = $this->get('security.context')->getToken()->getUser();

            $projects = $currentUser->getProjects();

            return $this->render('SgpcCoreBundle:Project:index.html.twig', array(
                        'projects' => $projects,
            ));
        } else {
            return $this->redirectToRoute('fos_user_registration_register');
        }
    }

    /**
     * Crea una nueva entidad Project
     */
    public function createAction(Request $request) {
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $model = $form->get('model')->getData();

            if ($model == 'kanban') {
                $storedList = new Listing();
                $storedList->setName('Archivadas');
                $storedList->setProject($project);
                $em->persist($storedList);
            }

            $currentUser = $this->get('security.context')->getToken()->getUser();
            $project->addUser($currentUser);
            $project->setOwner($currentUser);

            $em->persist($project);
            $em->flush();

            if ($model == 'kanban') {
                return $this->redirectToRoute('sgpc_project_kanban', array(
                            'id' => $project->getId()
                ));
            } else {
                return $this->redirectToRoute('sgpc_project_scrum', array(
                            'id' => $project->getId()
                ));
            }
        }

        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
                    'project' => $project,
                    'create_form' => $form->createView(),
        ));
    }

    /**
     * Crea el formulario para crear una entidad proyecto
     *
     * @param Project $entity The entity
     *
     * @return Form The form
     */
    private function createCreateForm(Project $entity) {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('sgpc_project_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Crear', 'attr' => ['class' => 'btn btn-success btn-sm']));
        return $form;
    }

    /**
     * Muestra el formulario de creacion de proyecto
     */
    public function addAction() {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
                    'entity' => $entity,
                    'create_form' => $form->createView(),
        ));
    }

    /**
     * Muestra una entidad proyecto tipo kanban
     */
    public function kanbanAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('SgpcCoreBundle:Project')->find($id);

        $lists = $project->getListings();

//        $activeTasks = $em->getRepository('SgpcCoreBundle:Task')
//                ->getActiveTasksForProject($id);

        $deleteForm = $this->createDeleteForm($id);
        $addmemberForm = $this->createAddMemberForm($id);

        if (!$project) {
            throw $this->createNotFoundException('No se ha encontrado la entidad proyecto.');
        }

        return $this->render('SgpcCoreBundle:Project:kanban.html.twig', array(
                    'lists' => $lists,
//                    'activeTasks' => $activeTasks,
                    'project' => $project,
                    'delete_form' => $deleteForm->createView(),
                    'addmember_form' => $addmemberForm->createView(),
        ));
    }

    /**
     * Muestra una entidad proyecto tipo kanban
     */
    public function scrumAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('SgpcCoreBundle:Project')->findOneById($id);

        $deleteForm = $this->createDeleteForm($id);
        $addmemberForm = $this->createAddMemberForm($id);

        if (!$project) {
            throw $this->createNotFoundException('No se ha encontrado la entidad proyecto.');
        }

        return $this->render('SgpcCoreBundle:Project:scrum.html.twig', array(
                    'project' => $project,
                    'delete_form' => $deleteForm->createView(),
                    'addmember_form' => $addmemberForm->createView(),
        ));
    }

    /**
     * Elimina la entidad proyecto
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository('SgpcCoreBundle:Project')->find($id);
            if (!$project) {
                throw $this->createNotFoundException('No se ha encontrado la entidad proyecto.');
            }
            $em->remove($project);
            $em->flush();
        }

        return $this->redirectToRoute('sgpc_core_homepage');
    }

    /**
     * Crea el form necesario para eliminar una entidad project
     *
     * @param mixed $id The entity id
     *
     * @return Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_project_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array(
                            'label' => 'Eliminar',
                            'attr' => array(
                                'class' => 'btn btn-danger btn-sm',
                                'onclick' => 'return confirm("Estás seguro que quieres eliminar el proyecto?")'
                    )))
                        ->getForm()
        ;
    }

    /*
     * Crea el formulario para añadir un miembro al proyecto
     */

    private function createAddMemberForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_project_addmember', array('id' => $id)))
                        ->setMethod('POST')
                        ->add('user')
                        ->add('submit', 'submit', array('label' => 'Añadir usuario'))
                        ->getForm();
    }

    /**
     * Add member to project.
     */
    public function addMemberAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createAddMemberForm($id);
        $form->handleRequest($request);

        $project = $em->getRepository('SgpcCoreBundle:Project')->findOneById($id);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $formuser = $form->getData('user');

            $user = $em->getRepository('SgpcCoreBundle:User')->findOneBy(array('username' => $formuser));
            if (!$user) {
                throw $this->createNotFoundException('No se ha encontrado el usuario.');
            }
            $project->addUser($user);
            $em->persist($project);
            $em->persist($user);
            $em->flush();

            if ($project->getModel() == 'kanban') {
                return $this->redirectToRoute('sgpc_project_kanban', array(
                            'id' => $project->getId()
                ));
            } else {
                return $this->redirectToRoute('sgpc_project_scrum', array(
                            'id' => $project->getId()
                ));
            }
        }

        return $this->render('SgpcCoreBundle:Project:addmember.html.twig', array(
                    'project' => $project,
                    'addmember_form' => $form->createView(),
        ));
    }

    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Task not found');
        }

        $form = $this->createEditForm($task);

        return $this->render('SgpcCoreBundle:Task:edit.html.twig', array('task' => $task, 'form' => $form->createView()));
    }

}
