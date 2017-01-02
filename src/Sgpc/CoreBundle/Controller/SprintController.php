<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\CoreBundle\Entity\Task;
use Sgpc\CoreBundle\Entity\Listing;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Entity\Sprint;
use Sgpc\CoreBundle\Form\SprintType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class SprintController extends Controller {

    /**
     * Crea una nueva entidad Sprint
     */
    public function createAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();

        $entity = new Sprint();
        $project = $this->getDoctrine()->getRepository('SgpcCoreBundle:Project')->findOneById($id);
        if (!$project) {
            throw $this->createNotFoundException('No se ha encontrado la entidad proyecto.');
        }
        $form = $this->createCreateForm($id);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $entity->setProject($project);
            $entity->setName('Sprint');
            $end = $form->get('end')->getData();
            $entity->setStart(new \Datetime);
            $entity->setEnd($end);

            $todoList = new Listing();
            $todoList->setName('To Do');
            $todoList->setSprint($entity);
            $em->persist($todoList);

            $doingList = new Listing();
            $doingList->setName('Doing');
            $doingList->setSprint($entity);
            $em->persist($doingList);

            $doneList = new Listing();
            $doneList->setName('Done');
            $doneList->setSprint($entity);
            $em->persist($doneList);


            $formTasks = $form->get('tasks')->getData();
            foreach ($formTasks as $formTask) {
                $task = $em->getRepository('SgpcCoreBundle:Task')->findOneBy(array('id' => $formTask));
                $task->setSprint($entity);
                if ($task->getLastListing() == null || $task->getLastListing() == 'To Do') {
                    $task->setListing($todoList);
                    $task->setLastListing($todoList->getName());
                } else if ($task->getLastListing() == 'Doing') {
                    $task->setListing($doingList);
                    $task->setLastListing($doingList->getName());
                } else {
                    $task->setListing($doneList);
                    $task->setLastListing($doneList->getName());
                }
                $task->setIsActive(True);
                $em->persist($task);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);

            $em->flush();

            return $this->redirect($this->generateUrl('sgpc_project_scrum', array('id' => $project->getId())));
        }

        return $this->render('SgpcCoreBundle:Sprint:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Display del form para crear una nueva entidad Sprint
     */
    public function addAction($id) {
        $entity = new Sprint();

        $form = $this->createCreateForm($id);

        return $this->render('SgpcCoreBundle:Sprint:add.html.twig', array(
                    'entity' => $entity,
                    'create_sprint_form' => $form->createView(),
        ));
    }

    private function createCreateForm($id) {

        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_sprint_create', array('id' => $id)))
                        ->setMethod('POST')
                        ->add('tasks', 'choice', array(
                            'placeholder' => 'Selecciona las tareas de este sprint',
                            'choices' => $this->tasksToChoices($id),
                            'multiple' => true
                        ))
                        ->add('end', 'date', [
                            'widget' => 'single_text',
                            'format' => 'dd-MM-yyyy',
                            'attr' => [
                                'class' => 'form-control input-inline datepicker',
                                'data-provide' => 'datepicker',
                                'data-date-format' => 'dd-mm-yyyy',
                            ]
                        ])
                        ->add('submit', 'submit', array(
                            'label' => 'Crear',
                            'attr' => array(
                                'class' => 'btn btn-danger btn-sm',
                    )))
                        ->getForm()
        ;
    }

    protected function tasksToChoices($id) {

        $project = $this->getDoctrine()->getRepository('SgpcCoreBundle:Project')->findOneById($id);

        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('SELECT t FROM SgpcCoreBundle:ScrumTask t JOIN t.project p WHERE p.id = :idProject AND t.isActive = false AND t.finished = false');

        $query->setParameters(array(
            'idProject' => $project->getId(),
        ));
        $tasks = $query->getResult();
        $choices = array();
        foreach ($tasks as $task) {
            $choices[$task->getId()] = $task->getName();
        }
        return $choices;
    }

    /**
     * Muestra la entidad Sprint
     */
    public function viewAction($id) {
        $em = $this->getDoctrine()->getManager();
        $sprint = $em->getRepository('SgpcCoreBundle:Sprint')->find($id);
        $project = $sprint->getProject();

        if (!$sprint) {
            throw $this->createNotFoundException('Unable to find Sprint entity.');
        }

        $storeForm = $this->createStoreForm($id);

        return $this->render('SgpcCoreBundle:Sprint:view.html.twig', array(
                    'sprint' => $sprint,
                    'project' => $project,
                    'store_form' => $storeForm->createView()
        ));
    }

    /* crea el formulario para finalizar un sprint */

    private function createStoreForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('sgpc_sprint_store', array('id' => $id)))
                        ->setMethod('POST')
                        ->add('submit', 'submit', array('label' => 'Finalizar sprint', 'attr' => array(
                                'class' => 'btn btn-danger btn-sm',
                                'onclick' => 'return confirm("Estás seguro que quieres finalizar el sprint?")'
                    )))
                        ->getForm();
    }

    /* Accion que se encarga de finalizar un sprint */

    public function storeAction(Request $request, $id) {
        $form = $this->createStoreForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $sprint = $em->getRepository('SgpcCoreBundle:Sprint')->find($id);
            $sprint->setEnd(new \Datetime);
            if (!$sprint) {
                throw $this->createNotFoundException('No se ha encontrado la entidad sprint.');
            }
            $projectId = $sprint->getProject()->getId();
            $tasks = $sprint->getTasks();

            foreach ($tasks as $task) {

                $newTask = clone $task;
                $em->persist($newTask);

                $listing = $task->getListing();
                if ($listing->getName() == 'Done') {
                    $task->setFinished(true);
                }
                $task->setIsActive(false);
                $task->setLastListing($listing->getName());
                $task->setListing(null);
                $em->persist($task);
            }

            $sprint->setIsActive(false);
            $em->persist($sprint);
            $em->flush();

            return $this->redirect($this->generateUrl('sgpc_project_scrum', array('id' => $projectId)));
        }
    }
    
     /**
     * Muestra el reporte del  Sprint
     */
    public function reportAction($id) {
        $em = $this->getDoctrine()->getManager();
        $sprint = $em->getRepository('SgpcCoreBundle:Sprint')->find($id);
        $project = $sprint->getProject();

        if (!$sprint) {
            throw $this->createNotFoundException('Unable to find Sprint entity.');
        }

        $storeForm = $this->createStoreForm($id);

        return $this->render('SgpcCoreBundle:Sprint:report.html.twig', array(
                    'sprint' => $sprint,
                    'project' => $project,
                    'store_form' => $storeForm->createView()
        ));
    }

}
