<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    public function indexAction()
    {
        return $this->render('SgpcCoreBundle:Project:index.html.twig');
    }
    
    public function addAction()
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        
        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('sgpc_project_add'),
            'method' => 'POST'
        ));
        
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->handleRequest($request);
        
        if ($form->isValid()) 
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();
            
            return $this->redirectToRoute('sgpc_core_homepage');
        }
        
        return $this->render('SgpcCoreBundle:Project:index.html.twig', array(
            'form' => $form->createView()
        ));

    }
}
