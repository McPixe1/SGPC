<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /*
     * Listado de proyectos del usuario que se encuentra logeado
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.authorization_checker');
        
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) 
        {
            $currentUser = $this->get('security.context')->getToken()->getUser();

            $projects = $currentUser->getProjects();

            return $this->render('SgpcCoreBundle:Project:index.html.twig', array(
              'projects' => $projects,
            ));     
        }
        else
        {
            return $this->redirectToRoute('fos_user_registration_register');
        }
    


    }
    
    
    /**
     * Crea una nueva entidad Project
     */
    public function createAction(Request $request)
    {
        $entity = new Project();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
           
            $currentUser = $this->get('security.context')->getToken()->getUser();
            $entity->addUser($currentUser);
            $em->persist($entity);
            $em->flush();
            
            return $this->redirectToRoute('sgpc_core_homepage');
        }
        
        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    
    
    /**
     * Crea el formulario para crear una entidad proyecto
     *
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('sgpc_project_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }
    
    
    /**
     * Muestra el formulario
     */
    public function addAction()
    {
        $entity = new Project();
        $form   = $this->createCreateForm($entity);
        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
}
