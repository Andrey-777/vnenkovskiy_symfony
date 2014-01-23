<?php
namespace Andrey\RssReaderBundle\Models;

use Andrey\RssReaderBundle\Entity\Chanels;
use Andrey\RssReaderBundle\Entity\News;
class Model {
    public function insertChanels($doctrine, Array $listChanels)
    {
        if(!$listChanels = $this->filterChanels($doctrine, $listChanels)) {
            return 0;
        }

        $em = $doctrine->getManager();
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

    public function insertNews($doctrine, Array $listNews)
    {
        if(!$listNews = $this->filterNews($doctrine, $listNews)) {
            return 0;
        }

        $em = $doctrine->getManager();

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

    protected function filterChanels($doctrine, Array $listChanels)
    {
        $repository = $doctrine->getRepository('AndreyRssReaderBundle:Chanels');

        foreach($listChanels as $key => $chanel) {
            if ($res = $repository->findByLink($chanel['link'])) {
                unset($listChanels[$key]);
            }
        }

        return $listChanels ? : false;
    }

    protected function filterNews($doctrine, Array $listNews)
    {
        $repository = $doctrine->getRepository('AndreyRssReaderBundle:News');

        foreach($listNews as $key => $news) {
            if ($res = $repository->findByHashCode($news['hashCode'])) {
                unset($listNews[$key]);
            }
        }

        return $listNews ? : false;
    }

    public function getAllChanels($doctrine)
    {
        return $doctrine->getRepository('AndreyRssReaderBundle:Chanels')->findAll();
    }

    public function getNewsById($doctrine, $id)
    {
        return $doctrine->getRepository('AndreyRssReaderBundle:News')->find($id);
    }

    public function getAllNews($doctrine)
    {
        return $doctrine->getRepository('AndreyRssReaderBundle:News')->findBy(array(), array('pubDate'=>'asc'));
    }
} 