<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Sgpc\CoreBundle\Entity\Listing;
use Sgpc\CoreBundle\Form\ListingType;

class ListingController extends Controller
{

    /**
     * Crea una nueva entidad Listing
     */
    public function createAction(Request $request, $id)
    {
        $entity = new Listing();
        $parent = $this->getDoctrine()->getRepository('SgpcCoreBundle:Project')->findOneById($id);        
        $form = $this->createForm(new ListingType(), $entity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $entity->setProject($parent);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('sgpc_project_view', array('id' => $entity->getProject()->getId())));
        }
        return $this->render('SgpcCoreBundle:Listing:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Display del form para crear una nueva entidad Listing
     */
    public function addAction($id)
    {
        $entity = new Listing();
          
        $form = $this->createForm(new ListingType(), $entity, array(
            'action' => $this->generateUrl('sgpc_listing_create', array('id' => $id)),
        ));  
        
        return $this->render('SgpcCoreBundle:Listing:add.html.twig', array(
            'entity' => $entity,
            'create_form'   => $form->createView(),
        ));
    }
}
