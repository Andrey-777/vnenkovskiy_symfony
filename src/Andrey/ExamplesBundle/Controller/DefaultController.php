<?php

namespace Andrey\ExamplesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AndreyExamplesBundle:Default:index.html.twig', array('name' => $name));
    }
}
