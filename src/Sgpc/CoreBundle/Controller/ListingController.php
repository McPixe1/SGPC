<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Sgpc\CoreBundle\Entity\Listing;

class ListingController extends Controller
{
    /**
     * Lists all Listing entities.
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('WacTechWebBundle:Listing')->findAll();
        return $this->render('WacTechWebBundle:Listing:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new Listing entity.
     */
    public function createAction(Request $request)
    {
        $entity = new Listing();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getProject()->getId())));
        }
        return $this->render('WacTechWebBundle:Listing:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
    /**
     * Creates a form to create a Listing entity.
     *
     * @param Listing $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Listing $entity)
    {
        $form = $this->createForm(new ListingType(), $entity, array(
            'action' => $this->generateUrl('listing_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }
    /**
     * Displays a form to create a new Listing entity.
     */
    public function newAction()
    {
        $entity = new Listing();
        $form   = $this->createCreateForm($entity);
        return $this->render('WacTechWebBundle:Listing:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
}
