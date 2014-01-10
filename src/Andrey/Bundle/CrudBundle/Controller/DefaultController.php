<?php

namespace Andrey\Bundle\CrudBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = null)
    {
       return $this->render('AndreyCrudBundle:Default:index.html.twig', array('name' => $name));
    }
}
