<?php

namespace Oforge\Engine\Core\Helper;

/**
 * Class PaginatorHelper
 *
 * @package Oforge\Engine\Core\Helper
 */
class PaginatorHelper {

    /** Prevent instance. */
    private function __construct() {
    }

    /**
     * @param int $itemsTotal
     * @param int $currentPage
     * @param int $entitiesPerPage
     *
     * @return array
     */
    public static function preparePaginatorData(int $itemsTotal, int $currentPage, int $entitiesPerPage) : array {
        $paginatorMax = ceil($itemsTotal / $entitiesPerPage);
        $offset       = null;
        if ($currentPage > 1) {
            $offset = ($currentPage - 1) * $entitiesPerPage;
        }

        return [
            'offset' => $offset,
            'limit'  => $entitiesPerPage,
            'total'  => $itemsTotal,
            'page'   => [
                'current' => $currentPage,
                'first'   => 1,
                'last'    => $paginatorMax,
            ],
        ];
    }

}
