<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class DefaultController extends Controller
{
    const COUNT_NEWS_ON_PAGE = 15;

    public function indexAction(Request $request)
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:index.html.twig',
                array('showPagin' => false)
                );
    }

    public function allAction($page)
    {
        $services = $this->getServices('all');

        return $this->render(
            'AndreyRssReaderBundle:Default:all.html.twig',
            array('allNews'     => $services['model']->getAllNews($services['doctrine'], self::COUNT_NEWS_ON_PAGE, $page,
                                                                $services['rssService']),
                  'page'        => $page,
                  'urlForPagin' => 'http://symfony/rss_reader/all/',
                  'showPagin'   => true,
                  'paginator'   => $services['rssService']->getPaginator($services['doctrine'],
                                                                       $services['paginService'],
                                                                       $services['model'],
                                                                       self::COUNT_NEWS_ON_PAGE, $page)
            ));
    }

    public function sourceAction()
    {
        $services = $this->getServices('source');

        return $this->render(
            'AndreyRssReaderBundle:Default:source.html.twig',
                array('chanels'   => $services['model']->getChanelsWithCountNews($services['doctrine']),
                      'showPagin' => false)
                );
    }

    public function sourcenewsAction($id, $page)
    {
        $services = $this->getServices('sourcenews');

        return $this->render(
            'AndreyRssReaderBundle:Default:sourcenews.html.twig',
                array('news'        => $services['model']->getNewsByChanel($services['doctrine'], $id,
                                                                         self::COUNT_NEWS_ON_PAGE,
                                                                         $page, $services['rssService']),
                      'page'        => $page,
                      'urlForPagin' => "http://symfony/rss_reader/sourcenews/id/$id/page/",
                      'showPagin'   => true,
                      'paginator'   => $services['rssService']->getPaginator($services['doctrine'],
                                                                           $services['paginService'],
                                                                           $services['model'],
                                                                           self::COUNT_NEWS_ON_PAGE, $page, $id)
                ));
    }

    public function newsAction($id)
    {
        $services = $this->getServices('news');

        return $this->render(
            'AndreyRssReaderBundle:Default:news.html.twig',
                array('news'      => $services['model']->getNewsById($services['doctrine'], $id),
                      'showPagin' => false
                ));
    }

    public function updateAction()
    {
        $services = $this->getServices('update');

        return $this->render(
            'AndreyRssReaderBundle:Default:updateResponse.html.twig',
                $services['rssService']->updateMethod($services['kernel'], $services['doctrine'], $services['model'])
                );
    }

    protected function getServices($action)
    {
        $services = array('doctrine' => $this->getDoctrine(),
                          'model'    => $this->get('RssReaderModel.model'));

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
