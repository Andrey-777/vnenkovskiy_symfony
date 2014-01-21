<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \Exception;
class DefaultController extends Controller
{
    public function indexAction()
    {


        return $this->render('AndreyRssReaderBundle:Default:index.html.twig');
    }

    public function updateAction()
    {
        try {
            $service = $this->get('RssReaderService.service');

            return $this->render(
                'AndreyRssReaderBundle:Default:updateResponse.html.twig',
                    $service->updateMethod($this->get('kernel'))
            );
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}
