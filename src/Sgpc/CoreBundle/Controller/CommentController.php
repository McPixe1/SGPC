<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sgpc\CoreBundle\Entity\Comment;
use Sgpc\CoreBundle\Form\CommentType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommentController extends Controller {

    public function addAction($id) {
        $task = $this->getTask($id);

        $comment = new Comment();
        $comment->setTask($task);
        $form = $this->createForm(new CommentType(), $comment);

        return $this->render('SgpcCoreBundle:Comment:add.html.twig', array(
                    'comment' => $comment,
                    'form' => $form->createView()
        ));
    }

    public function createAction($id) {
        $em = $this->getDoctrine()->getManager();

        $task = $this->getTask($id);
        $currentUser = $this->get('security.context')->getToken()->getUser();

        $comment = new Comment();
        $comment->setTask($task);
        $comment->setUser($currentUser);
        
        $request = $this->getRequest();
        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($comment);
            $em->flush();
            return $this->redirect($this->generateUrl('sgpc_task_view', array(
                                'id' => $comment->getTask()->getId())) .
                            '#comment-' . $comment->getId()
            );
        }

        return $this->render('SgpcCoreBundle:Comment:create.html.twig', array(
                    'comment' => $comment,
                    'form' => $form->createView()
        ));
    }

    protected function getTask($id) {
        $em = $this->getDoctrine()
                ->getEntityManager();

        $task = $em->getRepository('SgpcCoreBundle:Task')->find($id);

        if (!$task) {
            throw $this->createNotFoundException('Unable to find Task post.');
        }

        return $task;
    }

}
