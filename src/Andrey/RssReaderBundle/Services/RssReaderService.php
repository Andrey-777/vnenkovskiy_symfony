<?php
namespace Andrey\RssReaderBundle\Services;

use Symfony\Component\DependencyInjection\SimpleXMLElement;
use \Exception;
class RssReaderService {
    protected $_kernelService = null;
    protected $_modelService  = null;
    protected $_paginService  = null;
    protected $_listChanels   = array();
    protected $_listNews      = array();

    public function __construct($kernel, $pagin, $model)
    {
        $this->_kernelService = $kernel;
        $this->_paginService  = $pagin;
        $this->_modelService  = $model;
    }

    public function updateMethod()
    {
        $contentRss     = $this->_getContentRss($this->_getContentFile());
        $this->_populateNewsAndChanel($contentRss);

        $response['chanels'] = $this->_modelService->insertChanels($this->_listChanels);
        $this->chanelToNews();
        $response['news']    = $this->_modelService->insertNews($this->_listNews);

        return $response;
    }

    protected function _getContentFile()
    {
        try {
            $content    = file_get_contents($this->_kernelService->locateResource('@AndreyRssReaderBundle/Files/links.txt'));
            $linksToRss = array_filter(explode("\r\n", $content));

            return $linksToRss;
        } catch (Exception $e) {
            $this->errorMessage =  $e->getMessage();
        }
    }

    protected function _getContentRss($linksToRss)
    {
        foreach ($linksToRss as $itemLinkRss) {
            $contentRss[] = file_get_contents($itemLinkRss);
        }

        return $contentRss;
    }

    protected function _populateNewsAndChanel($contentsRss)
    {
        foreach ($contentsRss as $keyXMLFile => $itemXMLFile) {
            $sxml = new SimpleXMLElement($itemXMLFile, LIBXML_NOCDATA);
            $this->_listChanels[$keyXMLFile]['title'] = (string)$sxml->channel->title;
            $this->_listChanels[$keyXMLFile]['link']  = (string)$sxml->channel->link;

            foreach ($sxml->channel->item as $keyNews => $itemNews) {
                $news['title']       = $itemNews->title;
                $news['link']        = $itemNews->link;
                $news['description'] = strip_tags($itemNews->description);
                $news['hashCode']    = md5($itemNews->description);

                if ((string)$sxml->channel->link == 'http://tsn.ua/') {
                    preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i',
                        (string)$itemNews->description, $matches);
                    $news['enclosure'] = $matches[1][0];
                } else {
                    $news['enclosure']   = property_exists($itemNews, 'enclosure') ? $itemNews->enclosure['url'] : '';
                }
                $news['pubDate']    = date('Y-m-d H:i:s', strtotime($itemNews->pubDate));
                $news['linkChanel'] = $sxml->channel->link;
                $this->_listNews[]  = $news;
            }
        }
    }

    protected function chanelToNews()
    {
        foreach ($this->_modelService->getAllChanels() as $itemChanel) {
            foreach ($this->_listNews as $key => $itemNews) {

                if ($itemNews['linkChanel'] == $itemChanel->getLink()) {
                    $this->_listNews[$key]['linkChanel'] = $itemChanel->getId();
                }
            }
        }
    }

    public function getPaginator($countOnPage, $page, $sourceId = null)
    {
        if ($sourceId) {
            return $this->_paginService->paginator($page, $this->_modelService->getCountNews($sourceId), $countOnPage);
        } else {
            return $this->_paginService->paginator($page, $this->_modelService->getCountNews(), $countOnPage);
        }
    }
}
