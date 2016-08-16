<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Sgpc\CoreBundle\Entity\Task;
use Sgpc\CoreBundle\Form\TaskType;

class TaskController extends Controller
{
  public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SgpcCoreBundle:Task')->findAll();
        return $this->render('SgpcCoreBundle:Task:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Task entity.
     *
     * @param Request $request
     *
     * @return resource view
     */
    public function createAction(Request $request)
    {
        $entity = new Task();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('sgpc_task_view', array('id' => $entity->getId())));
        }
        return $this->render('SgpcCoreBundle:Task:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('sgpc_task_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }
    /**
     * Displays a form to create a new Task entity.
     */
    public function addAction()
    {
        $entity = new Task();
        $form   = $this->createCreateForm($entity);
        return $this->render('SgpcCoreBundle:Task:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Finds and displays a Task entity.
     *
     * @param int $id The Task entity
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);
        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
//        $deleteForm = $this->createDeleteForm($id);
        return $this->render('SgpcCoreBundle:Task:view.html.twig', array(
            'task'      => $task,
//            'delete_form' => $deleteForm->createView(),
        ));
    }
}
