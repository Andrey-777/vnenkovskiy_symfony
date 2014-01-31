<?php
namespace Andrey\RssReaderBundle\Models;

use \Doctrine\DBAL\DBALException;
use \Doctrine\ORM\NoResultException;
class Model {
    protected $_doctrine = null;
    protected $_logger   = null;
    protected $_isError  = false;
    const BATCH_INSERT_CHANELS = 20;
    const BATCH_INSERT_NEWS    = 50;

    public function __construct($doctrine, $logger)
    {
        $this->_doctrine = $doctrine;
        $this->_logger   = $logger;
    }

    public function insertChanels(array $listChanels)
    {
        $i = 0;

        try {
            $listChanels = $this->filterChanels($listChanels);

            if(!$listChanels) {
                return 0;
            }

            $em = $this->_doctrine->getManager();

            foreach ($listChanels as $chanel) {
                $em->persist($chanel);

                if (($i % self::BATCH_INSERT_CHANELS) == 0) {
                    $em->flush();
                    $em->clear();
                }

                $this->_counter++;
            }

            $em->flush();
            $em->clear();
        } catch(DBALException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Insert channels successfully completed');
            return $i;
        }
    }

    public function insertNews(array $listNews)
    {
        $i = 0;

        try {
            $listNews = $this->filterNews($listNews);

            if(!$listNews) {
                return 0;
            }

            $em = $this->_doctrine->getManager();

            foreach ($listNews as $news) {
                $em->persist($news);

                if (($i % self::BATCH_INSERT_NEWS) == 0) {
                    $em->flush();
                    $em->clear();
                }

                $i++;
            }

            $em->flush();
            $em->clear();

            return $i;
        } catch(DBALException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Insert news successfully completed');
            return $i;
        }
    }

    protected function filterChanels(array $listChanels)
    {
        $repository = $this->_doctrine->getRepository('AndreyRssReaderBundle:Chanels');

        foreach($listChanels as $key => $chanel) {
            if ($res = $repository->findByLink($chanel->getLink())) {
                unset($listChanels[$key]);
            }
        }

        return $listChanels ? : false;
    }

    protected function filterNews(array $listNews)
    {
        $repository = $this->_doctrine->getRepository('AndreyRssReaderBundle:News');

        foreach($listNews as $key => $news) {
            if ($res = $repository->findByHashCode($news->getHashCode())) {
                unset($listNews[$key]);
            }
        }

        return $listNews ? : false;
    }

    public function getAllChanels()
    {
        $channels = array();

        try {
            $channels = $this->_doctrine->getRepository('AndreyRssReaderBundle:Chanels')->findAll();
        } catch(NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Select all channels successfully completed');
            return $channels;
        }
    }

    public function getNewsById($id)
    {
        $news = null;

        try {
            $news = $this->_doctrine->getRepository('AndreyRssReaderBundle:News')->find($id);
        } catch(NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info("Select news with $id id successfully completed");
            return $news;
        }
    }

    public function getAllNews($count, $page)
    {
        $news = array();

        try {
            $news = $this->_doctrine
                                ->getRepository('AndreyRssReaderBundle:News')
                                    ->findBy(
                                            array(),
                                            array('pubDate'=>'desc'),
                                            $count,
                                            $page != 1 ? $count * ($page - 1) : 0
                                    );
        } catch (NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Select all news successfully completed');
            return $this->_changeDomainName($news);
        }
    }

    public function getChanelsWithCountNews()
    {
        $result = array();

        try {
            $qb    = $this->_doctrine->getManager()->createQueryBuilder();

            $query = $qb->select('chanels.id, chanels.title, chanels.link, count(news.id) as count_news')
                ->from('AndreyRssReaderBundle:Chanels', 'chanels')
                ->innerJoin('AndreyRssReaderBundle:News', 'news', 'WITH', 'chanels.id = news.chanelId')
                ->groupBy('chanels.id')
                ->getQuery();

            $result = $query->getResult();
        } catch(DBALException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        } catch(NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Select channel with count news successfully completed');
            return $result;
        }
    }

    public function getNewsByChanel($id, $count, $start)
    {
        $news = array();

        try {
            $news = $this->_doctrine->getRepository('AndreyRssReaderBundle:News')
                            ->findBy(array('chanelId' => $id),
                                     array('pubDate'=>'desc'),
                                     $count,
                                     $start != 1 ? $count * ($start - 1) : 0);
        } catch(NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Select news by id channel successfully completed');
            return $this->_changeDomainName($news);
        }
    }

    public function getCountNews($id = null)
    {
        $result = array();

        try {
            $qb    = $this->_doctrine->getManager()->createQueryBuilder();
            $query = $qb->select('count(news.id)')->from('AndreyRssReaderBundle:News', 'news');

            if ($id) {
                $query->where('news.chanelId = ' . $id);
            }

            $result = $query->getQuery()->getResult();
        } catch(DBALException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        } catch(NoResultException $e) {
            $this->_setIsError(true);
            $this->_logger->err($e->getMessage());
        }

        if (!$this->getIsError()) {
            $this->_logger->info('Select count news successfully completed');
            return $result[0][1];
        }
    }

    protected function _changeDomainName(array $news)
    {
        foreach($news as $itemNews) {
            preg_match('/^http\:\/\/(.*?)\/.*/i', $itemNews->getLink() . '/', $matches);
            $itemNews->setLink($matches[1]);
            echo var_dump($itemNews->getLink());
        }
        return $news;
    }

    public function  getIsError()
    {
        return $this->_isError;
    }

    protected function _setIsError($param)
    {
        $this->_isError = $param;
    }
} 