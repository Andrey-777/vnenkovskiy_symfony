<?php
namespace Andrey\RssReaderBundle\Services;

use Symfony\Component\DependencyInjection\SimpleXMLElement;
use \Exception;
class RssReaderService {
    const COUNT_NEWS_ON_PAGE = 15;

    public function updateMethod($kernel, $doctrine, $model)
    {
        $response = array();

        $linksToRss     = $this->_getContentFile($kernel);
        $contentRss     = $this->_getContentRss($linksToRss);
        $chanelsAndNews = $this->_populateNewsAndChanel($contentRss);

        $response['chanels']    = $model->insertChanels($doctrine, $chanelsAndNews['chanels']);
        $chanelsAndNews['news'] = $this->chanelToNews($chanelsAndNews['news'], $model, $doctrine);
        $response['news']       = $model->insertNews($doctrine, $chanelsAndNews['news']);

        return $response;
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
        $listChannels = array();
        $listNews     = array();

        foreach ($contentsRss as $keyXMLFile => $itemXMLFile) {
            $sxml = new SimpleXMLElement($itemXMLFile, LIBXML_NOCDATA);
            $listChannels[$keyXMLFile]['title'] = (string)$sxml->channel->title;
            $listChannels[$keyXMLFile]['link']  = (string)$sxml->channel->link;

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
                $listNews[] = $news;
            }
        }

        return array('chanels' => $listChannels, 'news' => $listNews);
    }

    protected function chanelToNews($listNews, $model, $doctrine)
    {
        $allChanels = $model->getAllChanels($doctrine);

        foreach ($allChanels as $row) {
            foreach ($listNews as $key => $itemNews) {

                if ($itemNews['linkChanel'] == $row->getLink()) {
                    $listNews[$key]['linkChanel'] = $row->getId();
                }
            }
        }

        return $listNews;
    }

    public function getDomainName($link)
    {
        preg_match('/^http\:\/\/(.*?)\/.*/i', $link . '/', $matches);

        return $matches[1];
    }
}
