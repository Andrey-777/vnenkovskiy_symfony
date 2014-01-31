<?php
namespace Andrey\RssReaderBundle\Services;

use Symfony\Component\DependencyInjection\SimpleXMLElement;
use Andrey\RssReaderBundle\Entity\Chanels;
use Andrey\RssReaderBundle\Entity\News;
use \Exception;
class RssReaderService {
    protected $_kernelService = null;
    protected $_modelService  = null;
    protected $_paginService  = null;
    protected $_logger        = null;
    protected $_listChanels   = array();
    protected $_listNews      = array();
    protected $_chanelUrls    = array('http://tsn.ua/');
    protected $_isError       = false;

    public function __construct($kernel, $pagin, $model, $logger)
    {
        $this->_kernelService = $kernel;
        $this->_paginService  = $pagin;
        $this->_modelService  = $model;
        $this->_logger        = $logger;
    }

    public function updateMethod()
    {
        $this->_populateNewsAndChanel($this->_getContentRss($this->_getContentFile()));
        $response['chanels'] = $this->_modelService->insertChanels($this->_listChanels);
        $this->_chanelToNews();
        $response['news']    = $this->_modelService->insertNews($this->_listNews);

        $this->_isError ? : $this->_logger->info('update successfully completed');

        return $response;
    }

    protected function _getContentFile()
    {
        $localResource = '@AndreyRssReaderBundle/Files/links.txt';

        try {
            return array_filter(
                explode(
                    "\n",
                    str_replace(
                        "\r",
                        '',
                        file_get_contents(
                            $this->_kernelService->locateResource($localResource)
                        )
                    )
                )
            );
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err('Unable to load ' . $localResource . ' file');
        }
    }

    protected function _getContentRss($linksToRss)
    {
        try {
            foreach ($linksToRss as $itemLinkRss) {
                $contentRss[] = file_get_contents($itemLinkRss);
            }

            return $contentRss;
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err('Unable to load ' . $itemLinkRss . ' file');
        }
    }

    protected function _populateNewsAndChanel($contentsRss)
    {
        try {
            foreach ($contentsRss as $itemXMLFile) {
                $sxml   = new SimpleXMLElement($itemXMLFile, LIBXML_NOCDATA);
                $chanel = new Chanels();
                $chanel->setTitle($sxml->channel->title)
                       ->setLink($sxml->channel->link);

                $this->_listChanels[] = $chanel;

                foreach ($sxml->channel->item as $itemNews) {
                    $news = new News();
                    $news->setTitle($itemNews->title)
                         ->setLink($itemNews->link)
                         ->setDescription(htmlspecialchars(strip_tags($itemNews->description)))
                         ->setHashCode(md5($itemNews->description))
                         ->setPubDate(date('Y-m-d H:i:s', strtotime($itemNews->pubDate)))
                         ->setChanelId($sxml->channel->link)
                         ->setImage($this->_listenerChanels((string)$sxml->channel->link, $itemNews));

                    $this->_listNews[]  = $news;
                }
            }
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err($e->getMessage());
        }
    }

    protected function _chanelToNews()
    {
        try {
            foreach ($this->_modelService->getAllChanels() as $itemChanel) {
                foreach ($this->_listNews as $key => $news) {
                    if ($news->getChanelId() == $itemChanel->getLink()) {
                        $this->_listNews[$key]->setChanelId($itemChanel->getId());
                    }
                }
            }
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err($e->getMessage());
        }
    }

    protected function _listenerChanels($url, $news)
    {
        try {
            if (!in_array($url, $this->_chanelUrls)) {
                return property_exists($news, 'enclosure') ? $news->enclosure['url'] : '';
            }

            switch($url) {
                case 'http://tsn.ua/':
                    preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i',
                        (string)$news->description, $matches);
                    return $matches[1][0];
                    break;
            }
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err($e->getMessage());
        }
    }

    public function getPaginator($countOnPage, $page, $sourceId = null)
    {
        try {
            if ($sourceId) {
                return $this->_paginService->paginator($page, $this->_modelService->getCountNews($sourceId), $countOnPage);
            } else {
                return $this->_paginService->paginator($page, $this->_modelService->getCountNews(), $countOnPage);
            }
        } catch (Exception $e) {
            $this->_isError = true;
            $this->_logger->err($e->getMessage());
        }
    }


}
