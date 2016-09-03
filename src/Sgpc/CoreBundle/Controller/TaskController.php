<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sgpc\CoreBundle\Entity\Task;
use Sgpc\CoreBundle\Form\TaskType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
    
    
     /*
     * Crea el formulario para añadir un miembro a la tarea, comprobando que solo
      * se puedan seleccionar los miembros del proyecto del cual depende la tarea
     */
    private function createAddMemberForm($id)
    {        
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
    protected function arrayToChoices($id){
        
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);
        $list = $task->getListing();
        $project = $list->getProject();
        $projectMembers = $project->getUsers()->toArray();
        $taskMembers = $task->getUsers()->toArray();
        
        /**
         * Si el miembro del proyecto ya es miembro de la tarea no lo metemos
        dentro de las choices del desplegable del formulario
        */
        foreach($projectMembers as $i => $projectMember){
            if(in_array($projectMember, $taskMembers)){
                unset($projectMembers[$i]);
            }
        }     
        foreach($projectMembers as $projectMember){         
            $choices[$projectMember->getId()] = $projectMember->getUsername();
        }
        if (empty($choices)) {
            return array('Todos son miembros');
        }
        return $choices;
    }
    
    
    /**
     *Añade un miembro a la tarea
     */
    public function addMemberAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createAddMemberForm($id);
        $form->handleRequest($request);
        
        $task = $em->getRepository('SgpcCoreBundle:Task')->findOneById($id);
        
        if ($form->isValid()) {
            
            $formuserId = $form->get('user')->getData();
       
            $user = $em->getRepository('SgpcCoreBundle:User')->findOneBy(array('id' => $formuserId));
            if(!$user){
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
            'addmember_form'   => $form->createView(),
        ));
    }
    
}
