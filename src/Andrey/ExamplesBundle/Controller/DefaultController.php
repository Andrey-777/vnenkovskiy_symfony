<?php

namespace Andrey\ExamplesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Andrey\ExamplesBundle\Command\MyCommand;
use Symfony\Component\Console\Application;
class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AndreyExamplesBundle:Default:index.html.twig');
    }
}
