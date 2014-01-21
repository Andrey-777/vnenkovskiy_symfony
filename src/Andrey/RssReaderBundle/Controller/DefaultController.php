<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $kernel = $container->getService('kernel');
        $path = $kernel->locateResource('@AdmeDemoBundle/path/to/file/Foo.txt');

        return $this->render('AndreyRssReaderBundle:Default:index.html.twig');
    }
}
