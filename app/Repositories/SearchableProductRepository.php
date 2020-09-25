<?php

namespace App\Repositories;

use App\SearchableModels\SearchableModel;
use App\SearchableModels\SearchableProductModel;

class SearchableProductRepository
{
    /**
     * @param array $filter
     * @param $from
     * @param int $size
     * @return mixed
     */
    public function match(array $filter, $from, int $size)
    {
        $params = [
            'index' => SearchableProductModel::INDEX_NAME,
            'from' => $from,
            'size' =>  $size,
            'body'  => [
                'query' => [
                    'match' => $filter
                ]
            ]
        ];

        return SearchableModel::client()->search($params)['hits']['hits'];
    }

    /**
     * @param $from
     * @param int $size
     * @return mixed
     */
    public function matchAll($from, int $size)
    {
        $params = [
            'index' => SearchableProductModel::INDEX_NAME,
            'from' => $from,
            'size' => $size
        ];

        return SearchableModel::client()->search($params)['hits']['hits'];
    }
}
