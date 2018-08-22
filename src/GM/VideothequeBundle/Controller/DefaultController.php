<?php

namespace GM\VideothequeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('GMVideothequeBundle:Default:index.html.twig');
    }
}
