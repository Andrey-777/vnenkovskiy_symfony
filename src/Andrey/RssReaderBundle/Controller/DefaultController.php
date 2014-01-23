<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AndreyRssReaderBundle:Default:index.html.twig');
    }

    public function allAction()
    {
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:all.html.twig',
                array('allNews' => $model->getAllNews($doctrine)));
    }

    public function sourceAction()
    {
        return $this->render('AndreyRssReaderBundle:Default:source.html.twig');
    }

    public function newsAction($id)
    {
        echo var_dump($id);
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:news.html.twig',
                array('news' => $model->getNewsById($doctrine, $id)));
    }

    public function updateAction()
    {
        $service  = $this->get('RssReaderService.service');
        $kernel   = $this->get('kernel');
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:updateResponse.html.twig',
                $service->updateMethod($kernel, $doctrine, $model)
        );
    }
}
