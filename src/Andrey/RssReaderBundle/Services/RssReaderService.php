<?php
namespace Andrey\RssReaderBundle\Services;

use Symfony\Component\DependencyInjection\SimpleXMLElement;
use \Exception;
class RssReaderService {
    protected $_listChannels = array();
    protected $_listNews     = array();

    public function updateMethod($kernel, $em, $model)
    {
        $linksToRss = $this->_getContentFile($kernel);
        $contentRss = $this->_getContentRss($linksToRss);
        $this->_populateNewsAndChanel($contentRss);

        $model->insertChanels($em, $this->_listChannels);

//        return "GOOD INSERT CHANEL";
        return array();
    }

    protected function _getContentFile($kernel)
    {
        try {
            $content    = file_get_contents($kernel->locateResource('@AndreyRssReaderBundle/Files/links.txt'));
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
            $this->_listChannels[$keyXMLFile]['title'] = (string)$sxml->channel->title;
            $this->_listChannels[$keyXMLFile]['link']  = (string)$sxml->channel->link;

            $chanels[] = $sxml->channel;

            foreach ($sxml->channel->item as $keyNews => $itemNews) {
                $news[] = $itemNews;

                $itemNewsForArr['title']       = $itemNews->title;
                $itemNewsForArr['link']        = $itemNews->link;
                $itemNewsForArr['description'] = $itemNews->description;

                if ((string)$sxml->channel->link == 'http://tsn.ua/') {
                    preg_match_all('/<img(?:\\s[^<>]*?)?\\bsrc\\s*=\\s*(?|"([^"]*)"|\'([^\']*)\'|([^<>\'"\\s]*))[^<>]*>/i',
                        (string)$itemNews->description, $matches);
                    $itemNewsForArr['enclosure'] = $matches[1][0];
                } else {
                    $itemNewsForArr['enclosure']   = property_exists($itemNews, 'enclosure') ? $itemNews->enclosure['url'] : '';
                }
                $itemNewsForArr['pubDate']    = $itemNews->pubDate;
                $itemNewsForArr['linkChanel'] = $sxml->channel->link;
                $this->_listNews[]            = $itemNewsForArr;
            }
        }
    }
}
