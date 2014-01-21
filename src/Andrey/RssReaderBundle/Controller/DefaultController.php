<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AndreyRssReaderBundle:Default:index.html.twig');
    }
}
