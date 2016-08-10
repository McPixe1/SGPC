<?php

namespace Sgpc\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\UserBundle\Entity\User;
use Sgpc\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $users = $em->getRepository('SgpcUserBundle:User')->findAll();
        
        return $this->render('SgpcUserBundle:User:index.html.twig', array('users'=> $users));
    }
    
    public function addAction()
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        
        return $this->render('SgpcUserBundle:User:add.html.twig', array('form' => $form->createView()));
        
    }
    
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('sgpc_user_create'),
            'method' => 'POST'
        ));
        return $form;
    }
    
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);
        
        if($form->isValid())
        {
            $password = $form->get('password')->getData();
            
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encoded);
            
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            
            return $this->redirectToRoute('sgpc_user_homepage');
        }
        return $this->render('SgpcUserBundle:User:add.html.twig', array('form' => $form->createView()));
    }
    
}
