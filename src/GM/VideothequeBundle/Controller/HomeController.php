<?php

namespace GM\VideothequeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends Controller
{
    public function homeAction()
    {
        return $this->render('GMVideothequeBundle:home:home.html.twig');
    }
}