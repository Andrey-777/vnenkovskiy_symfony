<?php
/**
 * Created by PhpStorm.
 * User: avnenkovskyi
 * Date: 1/21/14
 * Time: 4:54 PM
 */

namespace Andrey\RssReaderBundle\Models;

use Andrey\RssReaderBundle\Entity\Chanels;
use Andrey\RssReaderBundle\Entity\News;
class Model {
    public function insertChanels($em, Array $listChanels)
    {
        $batchSize = 10;

        for ($i = 1; $i <= count($listChanels); ++$i) {
            $chanel = new Chanels();
            $itemChanel = $listChanels[$i - 1];
            $chanel->setTitle($itemChanel['title']);
            $chanel->setLink($itemChanel['link']);

            $em->persist($chanel);

            if (($i % $batchSize) == 0) {
                $em->flush();
                $em->clear();
            }
        }

        $em->flush();
        $em->clear();
    }
} 