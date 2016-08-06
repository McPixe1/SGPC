<?php

namespace Sgpc\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SgpcUserBundle:Default:index.html.twig');
    }
}
