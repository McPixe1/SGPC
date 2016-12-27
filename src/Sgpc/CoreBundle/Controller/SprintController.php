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
            $entity->setName('Primer sprint');
            $entity->setStatus('inactivo');

            $formTasks = $form->get('tasks')->getData();
            foreach ($formTasks as $formTask) {
                $task = $em->getRepository('SgpcCoreBundle:Task')->findOneBy(array('id' => $formTask));
                $task->setSprint($entity);
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
                        ->add('submit', 'submit', array(
                            'label' => 'Crear',
                            'attr' => array(
                                'class' => 'btn btn-danger btn-sm',
                    )))
                        ->getForm()
        ;
    }

    protected function tasksToChoices($id) {

        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('SgpcCoreBundle:Task')->getActiveTasksForProject($id);

        $choices = array();
        foreach ($tasks as $task) {
            $choices[$task->getId()] = $task->getName();
        }
//        dump($choices);
        return $choices;
    }

    /**
     * Muestra la entidad Sprint
     */
    public function viewAction($id) {
        $em = $this->getDoctrine()->getManager();
        $sprint = $em->getRepository('SgpcCoreBundle:Sprint')->find($id);

        if (!$sprint) {
            throw $this->createNotFoundException('Unable to find Sprint entity.');
        }

        return $this->render('SgpcCoreBundle:Task:view.html.twig', array(
                    'sprint' => $sprint,
        ));
    }

}
