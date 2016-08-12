<?php

namespace Sgpc\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    public function indexAction()
    {
        return $this->render('SgpcCoreBundle:Project:index.html.twig');
    }
}
