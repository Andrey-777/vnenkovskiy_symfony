<?php

namespace Andrey\RssReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Exception;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
class DefaultController extends Controller
{
    const COUNT_NEWS_ON_PAGE = 15;

    public function __construct()
    {

    }

    public function indexAction(Request $request)
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:index.html.twig',
            array('showPagin' => false)
        );
    }

    public function allAction($page)
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:all.html.twig',
            array('allNews'     => $this->get('RssReaderModel.model')
                                        ->getAllNews(self::COUNT_NEWS_ON_PAGE, $page),
                  'page'        => $page,
                  'urlForPagin' => 'http://symfony/rss_reader/all/',
                  'showPagin'   => true,
                  'paginator'   => $this->get('RssReaderService.service')
                                        ->getPaginator(self::COUNT_NEWS_ON_PAGE, $page)
            )
        );
    }

    public function sourceAction()
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:source.html.twig',
            array('chanels'   => $this->get('RssReaderModel.model')->getChanelsWithCountNews(),
                  'showPagin' => false)
        );
    }

    public function sourcenewsAction($id, $page)
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:sourcenews.html.twig',
            array('news'        => $this->get('RssReaderModel.model')
                                        ->getNewsByChanel($id, self::COUNT_NEWS_ON_PAGE, $page),
                  'page'        => $page,
                  'urlForPagin' => "http://symfony/rss_reader/sourcenews/id/$id/page/",
                  'showPagin'   => true,
                  'paginator'   => $this->get('RssReaderService.service')
                                        ->getPaginator(self::COUNT_NEWS_ON_PAGE, $page, $id)
            )
        );
    }

    public function newsAction($id)
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:news.html.twig',
            array('news'      => $this->get('RssReaderModel.model')
                                      ->getNewsById($id),
                  'showPagin' => false
            )
        );
    }

    public function updateAction()
    {
        return $this->render(
            'AndreyRssReaderBundle:Default:updateResponse.html.twig',
            $this->get('RssReaderService.service')->updateMethod()
        );
    }
}

