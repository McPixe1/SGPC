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
    
    public function addAction()
    {
        $task = new Project();
        $form = $this->createCreateForm($project);
        
        return $this->render('SgpcUserBundle:Project:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    private function createCreateForm() 
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
           'action' => $this->generateUrl('sgpc_project_create'),
           'method' => 'POST'
        ));
        
        return $form;
    }
    
}
