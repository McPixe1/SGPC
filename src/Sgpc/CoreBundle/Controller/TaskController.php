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

    /**
     * Crea una nueva entidad Task
     */
    public function createAction(Request $request, $id)
    {
        $entity = new Task();
        $parent = $this->getDoctrine()->getRepository('SgpcCoreBundle:Listing')->findOneById($id);        
        $form = $this->createForm(new TaskType(), $entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $entity->setListing($parent);
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
     * Display del form para crear una nueva entidad Task
     */
    public function addAction($id)
    {
        $entity = new Task();
        
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('sgpc_task_create', array('id' => $id)),
        ));  
        
        return $this->render('SgpcCoreBundle:Task:add.html.twig', array(
            'entity' => $entity,
            'create_form'   => $form->createView(),
        ));
    }
    /**
     * Muestra la entidad Task
     */
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);
        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task entity.');
        }
        return $this->render('SgpcCoreBundle:Task:view.html.twig', array(
            'task'      => $task,
        ));
    }
}
