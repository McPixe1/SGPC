<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\CoreBundle\Entity\Task;
use Sgpc\CoreBundle\Entity\Listing;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Entity\Sprint;
use Sgpc\CoreBundle\Entity\Story;
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
            if (!$sprint) {
                throw $this->createNotFoundException('No se ha encontrado la entidad sprint.');
            }
            $projectId = $sprint->getProject()->getId();
            $tasks = $sprint->getTasks();

            $story = new Story();

            foreach ($tasks as $task) {

                $newTask = clone $task;
                $task->setSprint($sprint);
                $task->setStory($story);

                $story->setEnd(new \Datetime);
                $story->setSprint($sprint);

                $listing = $task->getListing();
                if ($listing->getName() == 'Done') {
                    $newTask->setFinished(true);
                }
                $newTask->setIsActive(false);
                $newTask->setLastListing($listing->getName());
                $newTask->setListing(null);
                $newTask->setSprint(null);
                
                $em->persist($task);
                $em->persist($newTask);
                $em->persist($story);
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
        $story = $sprint->getStories();
        $storyId = $sprint->getStories()->getId();

        if (!$sprint) {
            throw $this->createNotFoundException('Unable to find Sprint entity.');
        }

        $query = $em->createQuery('SELECT t FROM SgpcCoreBundle:ScrumTask t JOIN t.story s WHERE s.id = :idStory');
        $query->setParameters(array(
            'idStory' => $storyId
        ));
        $tasks = $query->getResult();


        $query2 = $em->createQuery('SELECT SUM(t.hours) as estimatedHours FROM SgpcCoreBundle:ScrumTask t JOIN t.sprint s WHERE s.id = :idSprint');
        $query2->setParameters(array(
            'idSprint' => $id
        ));
        $idealHours = $query2->getSingleScalarResult();


        $startDate = $sprint->getStart();
        $endDate = $sprint->getEnd();

        //Calculamos la duracion contando el inicio y el fin, por eso +2
        $interval = $endDate->diff($startDate)->format('%a');
        $interval = $interval + 2;

        //Calculamos el valor del eje de las X
        $xAxis = array();
        for ($i = 0; $i < $interval; $i++) {
            $xAxis[] = 'Dia ' . ($i + 1);
        }

        //Calculamos el valor de la linea de esfuerzo ideal
        $idealInterval = $idealHours / ($interval - 1);
        $idealArray = array();
        $hours = $idealHours;
        $copyInterval = 0;
        for ($i = 0; $i < $interval; $i++) {
            $idealArray[] = $hours - $copyInterval;
            $hours = $hours - $copyInterval;
            $copyInterval = $idealInterval;
        }

//        //Calculamos el valor de la linea de esfuerzo real
        $tasksIds = array();
        foreach ($tasks as $task) {
            $taskIds[] = $task->getId();
        }
        dump($taskIds);
        $query3 = $em->createQuery('SELECT w FROM SgpcCoreBundle:Worklog w JOIN w.task t WHERE t.story = :story');
        $query3->setParameters(array(
            'story' => $storyId
        ));
        $realHours = $query3->getResult();

        dump($realHours);

        return $this->render('SgpcCoreBundle:Sprint:report.html.twig', array(
                    'sprint' => $sprint,
                    'story' => $story,
                    'tasks' => $tasks,
                    'project' => $project,
                    'idealHours' => $idealHours,
                    'idealArray' => $idealArray,
                    'xAxis' => $xAxis,
                    'interval' => $interval
        ));
    }

}
