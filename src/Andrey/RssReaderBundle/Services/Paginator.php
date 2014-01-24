<?php
/**
 * Created by PhpStorm.
 * User: avnenkovskyi
 * Date: 1/23/14
 * Time: 6:05 PM
 */

namespace Andrey\RssReaderBundle\Services;


class Paginator {
    public function paginator($numberPage, $total, $countElements, $pagesOnPage = 5)
    {
        $countPages = (int)ceil($total / $countElements);

        $pages = array('startFor'     => 1,
            'endFor'       => 6,
            'firstPage'    => 1,
            'nextPage'     => $numberPage + 1,
            'previousPage' => $numberPage - 1,
            'lastPage'     => $countPages);

        $pages['hasNextPage']     = $numberPage != $countPages ? true : false;
        $pages['hasPreviousPage'] = $numberPage > $pages['firstPage'] ? true : false;
        $pages['isFirstPage']     = $numberPage > $pagesOnPage + 1 ? true : false;
        $pages['isLastPage']      = $numberPage < ($countPages - $pagesOnPage) ? true : false;

        if ($numberPage == $countPages) {
            $pages['startFor'] = $numberPage - $pagesOnPage;
            $pages['endFor']   = $numberPage;
        } else {
            $pages['startFor'] = $numberPage > $pagesOnPage ? $numberPage - $pagesOnPage : 1;
            $pages['endFor']   =
                ($numberPage + $pagesOnPage) > $countPages
                    ? $countPages
                    : $numberPage + $pagesOnPage;
        }

        return $pages;
    }
} 