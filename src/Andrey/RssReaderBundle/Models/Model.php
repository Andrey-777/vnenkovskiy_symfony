<?php
namespace Andrey\RssReaderBundle\Models;

use Andrey\RssReaderBundle\Entity\Chanels;
use Andrey\RssReaderBundle\Entity\News;
class Model {
    protected $_doctrine   = null;

    public function __construct($doctrine)
    {
        $this->_doctrine   = $doctrine;
    }

    public function insertChanels(Array $listChanels)
    {
        $listChanels = $this->filterChanels($listChanels);

        if(!$listChanels) {
            return 0;
        }

        $em = $this->_doctrine->getManager();
        $i = 0;

        foreach ($listChanels as $itemChanel) {
            $chanel = new Chanels();
            $chanel->setTitle($itemChanel['title']);
            $chanel->setLink($itemChanel['link']);

            $em->persist($chanel);

            if (($i % 20) == 0) {
                $em->flush();
                $em->clear();
            }

            $i++;
        }

        $em->flush();
        $em->clear();

        return $i;
    }

    public function insertNews(Array $listNews)
    {
        $listNews = $this->filterNews($listNews);

        if(!$listNews) {
            return 0;
        }

        $em = $this->_doctrine->getManager();

        $i = 1;

        foreach ($listNews as $itemNews) {
            $news = new News();
            $news->setTitle($itemNews['title']);
            $news->setLink($itemNews['link']);
            $news->setDescription($itemNews['description']);
            $news->setImage($itemNews['enclosure']);
            $news->setPubDate($itemNews['pubDate']);
            $news->setChanelId($itemNews['linkChanel']);
            $news->setHashCode($itemNews['hashCode']);

            $em->persist($news);

            if (($i % 20) == 0) {
                $em->flush();
                $em->clear();
            }

            $i++;
        }

        $em->flush();
        $em->clear();

        return $i;
    }

    protected function filterChanels(Array $listChanels)
    {
        $repository = $this->_doctrine->getRepository('AndreyRssReaderBundle:Chanels');

        foreach($listChanels as $key => $chanel) {
            if ($res = $repository->findByLink($chanel['link'])) {
                unset($listChanels[$key]);
            }
        }

        return $listChanels ? : false;
    }

    protected function filterNews(Array $listNews)
    {
        $repository = $this->_doctrine->getRepository('AndreyRssReaderBundle:News');

        foreach($listNews as $key => $news) {
            if ($res = $repository->findByHashCode($news['hashCode'])) {
                unset($listNews[$key]);
            }
        }

        return $listNews ? : false;
    }

    public function getAllChanels()
    {
        return $this->_doctrine->getRepository('AndreyRssReaderBundle:Chanels')->findAll();
    }

    public function getNewsById($id)
    {
        return $this->_doctrine->getRepository('AndreyRssReaderBundle:News')->find($id);
    }

    public function getAllNews($count, $page)
    {
        $news = $this->_doctrine->getRepository('AndreyRssReaderBundle:News')
                        ->findBy(array(), array('pubDate'=>'desc'),
                            $count,
                            $page != 1 ? $count * ($page - 1) : 0);

        return $this->changeDomainName($news);
    }

    public function getChanelsWithCountNews()
    {
        $qb    = $this->_doctrine->getManager()->createQueryBuilder();
        $query = $qb->select('chanels.id, chanels.title, chanels.link, count(news.id) as count_news')
                    ->from('AndreyRssReaderBundle:Chanels', 'chanels')
                    ->innerJoin('AndreyRssReaderBundle:News', 'news', 'WITH', 'chanels.id = news.chanelId')
                    ->groupBy('chanels.id')
                    ->getQuery();

        return $query->getResult();
    }

    public function getNewsByChanel($id, $count, $start)
    {
        $news = $this->_doctrine->getRepository('AndreyRssReaderBundle:News')
                        ->findBy(array('chanelId' => $id),
                                 array('pubDate'=>'desc'),
                                 $count,
                                 $start != 1 ? $count * ($start - 1) : 0);

        return $this->changeDomainName($news);
    }

    public function getCountNews($id = null)
    {
        $qb     = $this->_doctrine->getManager()->createQueryBuilder();
        $query = $qb->select('count(news.id)')->from('AndreyRssReaderBundle:News', 'news');

        if ($id) {
            $query->where('news.chanelId = ' . $id);
        }

        $result = $query->getQuery()->getResult();
        return $result[0][1];
    }

    public function changeDomainName(Array $news)
    {
        foreach($news as $itemNews) {
            preg_match('/^http\:\/\/(.*?)\/.*/i', $itemNews->getLink() . '/', $matches);
            $itemNews->setLink($matches[1]);
        }
        return $news;
    }
} 