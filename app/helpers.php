<?php

use Illuminate\Pagination\Paginator;

if (!function_exists('manualPagination')) {
    function manualPagination($results, $page, $pageSize) {
        $startingPoint = ($page * $pageSize) - $pageSize;

        $results = array_slice($results, $startingPoint, $pageSize, true);

        $results = new Paginator($results, $pageSize, $page);

        return $results;
    }
}
