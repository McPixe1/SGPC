<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sgpc\CoreBundle\Entity\Project;
use Sgpc\CoreBundle\Form\ProjectType;
use Sgpc\CoreBundle\Entity\Listing;
use Sgpc\CoreBundle\Form\ListingType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /*
     * Muestra el listado de proyectos del usuario si esta logeado,
     * en caso contrario lo redirige al registro
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
        $project = new Project();
        $form = $this->createCreateForm($project);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            
            $em = $this->getDoctrine()->getManager();
           
            $currentUser = $this->get('security.context')->getToken()->getUser();
            $project->addUser($currentUser);
            $em->persist($project);
            $em->flush();
            
            return $this->redirectToRoute('sgpc_project_view', array('id' => $project->getId()));
        }
        
        return $this->render('SgpcCoreBundle:Project:add.html.twig', array(
            'project' => $project,
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
    
    /**
     * Muestra una entidad proyecto
     */
    public function viewAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('SgpcCoreBundle:Project')->find($id);
        
        $deleteForm = $this->createDeleteForm($id);
        
        $members = $project->getUsers();
        
        
        $query = $em->createQuery(
                'SELECT l
                FROM SgpcCoreBundle:Listing l
                JOIN l.project p
                WHERE p.id = :projectId
                ORDER BY l.id ASC'
        )->setParameter('projectId', $id);
        
        $entities = $query->getResult();
        
        
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Project entity.');
        }
        
        return $this->render('SgpcCoreBundle:Project:view.html.twig', array(
            'project'      => $project,
            'delete_form' => $deleteForm->createView(),
            'members'       => $members,
            'entities' => $entities,
        ));
    }
    
    /**
     * Elimina la entidad proyecto
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $project = $em->getRepository('SgpcCoreBundle:Project')->find($id);
            if (!$project) {
                throw $this->createNotFoundException('Unable to find Project entity.');
            }
            $em->remove($project);
            $em->flush();
        }
        return $this->redirectToRoute('sgpc_core_homepage');
    }
    
    /**
     * Crea el form necesario para eliminar una entidad project
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sgpc_project_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => ['class' => 'btn btn-default btn-sm']))
            ->getForm()
        ;
    }
}