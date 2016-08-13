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
     * Muestra todos los listados del proyecto
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('SgpcCoreBundle:Listing')->findAll();
        return $this->render('SgpcCoreBundle:Listing:index.html.twig', array(
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
            return $this->redirect($this->generateUrl('sgpc_project_view', array('id' => $entity->getProject()->getId())));
        }
        return $this->render('SgpcCoreBundle:Listing:add.html.twig', array(
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
            'action' => $this->generateUrl('sgpc_listing_create'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Create'));
        return $form;
    }
    /**
     * Displays a form to create a new Listing entity.
     */
    public function addAction()
    {
        $entity = new Listing();
        $form   = $this->createCreateForm($entity);
        return $this->render('SgpcCoreBundle:Listing:add.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }
}
