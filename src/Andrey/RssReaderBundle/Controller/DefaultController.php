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
        $service  = $this->get('RssReaderService.service');
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');
        $allNews  = $model->getAllNews($doctrine);

        foreach($allNews as $news) {
            $news->setLink($service->getDomainName($news->getLink()));
        }

        return $this->render(
            'AndreyRssReaderBundle:Default:all.html.twig',
                array('allNews' => $allNews));
    }

    public function sourceAction()
    {
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:source.html.twig',
                array('chanels' => $model->getChanelsWithCountNews($doctrine))
        );
    }

    public function sourcenewsAction($id)
    {
        $service  = $this->get('RssReaderService.service');
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');
        $allNews  = $model->getNewsByChanel($doctrine, $id);

        foreach($allNews as $news) {
            $news->setLink($service->getDomainName($news->getLink()));
        }

        return $this->render(
            'AndreyRssReaderBundle:Default:sourcenews.html.twig',
                array('news' => $allNews)
        );
    }

    public function newsAction($id)
    {
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
