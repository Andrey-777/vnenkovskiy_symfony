<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class DefaultController extends Controller
{
    const COUNT_NEWS_ON_PAGE = 15;

    public function indexAction()
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:index.html.twig',
                array('showPagin' => false)
                );
    }

    public function allAction($page)
    {
        $rssService   = $this->get('RssReaderService.service');
        $doctrine     = $this->getDoctrine();
        $model        = $this->get('RssReaderModel.model');
        $paginService = $this->get('Paginator.service');

        return $this->render(
            'AndreyRssReaderBundle:Default:all.html.twig',
                array('allNews'   => $model->getAllNews($doctrine, self::COUNT_NEWS_ON_PAGE, $page, $rssService),
                      'page'      => $page,
                      'showPagin' => true,
                      'paginator' => $rssService->getPaginator($doctrine, $paginService, $model,
                                                    self::COUNT_NEWS_ON_PAGE, $page)
            ));
    }

    public function sourceAction()
    {
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:source.html.twig',
                array('chanels'   => $model->getChanelsWithCountNews($doctrine),
                      'showPagin' => false)
                );
    }

    public function sourcenewsAction($id, $page)
    {
        $rssService   = $this->get('RssReaderService.service');
        $doctrine     = $this->getDoctrine();
        $model        = $this->get('RssReaderModel.model');
        $paginService = $this->get('Paginator.service');

        return $this->render(
            'AndreyRssReaderBundle:Default:sourcenews.html.twig',
                array('news'      => $model->getNewsByChanel($doctrine, $id, self::COUNT_NEWS_ON_PAGE,
                                            $page, $rssService),
                      'page'      => $page,
                      'showPagin' => true,
                      'paginator' => $rssService->getPaginator($doctrine, $paginService, $model,
                                                    self::COUNT_NEWS_ON_PAGE, $page, $id)
                ));
    }

    public function newsAction($id)
    {
        $doctrine = $this->getDoctrine();
        $model    = $this->get('RssReaderModel.model');

        return $this->render(
            'AndreyRssReaderBundle:Default:news.html.twig',
                array('news'      => $model->getNewsById($doctrine, $id),
                      'showPagin' => false
                ));
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

    protected function getServices($action)
    {
        $services = array('doctrine' => $this->getDoctrine(), 'model' => $this->get('RssReaderModel.model'));

        if ($action == 'all' || $action == 'sourcenews') {
            $services['rssService']   = $this->get('RssReaderService.service');
            $services['paginService'] = $this->get('Paginator.service');
        } elseif ($action == 'update') {
            $services['rssService'] = $this->get('RssReaderService.service');
            $services['kernel']     = $this->get('kernel');
        }

        return $services;
    }
}
