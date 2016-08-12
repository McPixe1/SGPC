<?php

namespace Sgpc\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\UserBundle\Entity\Project;
use Sgpc\UserBundle\Form\ProjectType;

class ProjectController extends Controller
{
    
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $dql = "SELECT p FROM SgpcUserBUndle:Project p ORDER BY p.id DESC";
        $projects = $em->createQuery($dql);
        
        return $this->render('SgpcUserBundle:Project:index.html.twig', array('projects'=> $projects));
    }
    
    public function addAction()
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        
        return $this->render('SgpcUserBundle:Project:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function createCreateForm(Project $entity) 
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
           'action' => $this->generateUrl('sgpc_project_create'),
           'method' => 'POST'
        ));
        
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            return $this->redirectToRoute('sgpc_project_index');
        }
        
        return $this->render('SgpcUserBundle:Project:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
}
