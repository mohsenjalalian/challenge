<?php

namespace App\Repositories;

use App\SearchableModels\SearchableModel;
use App\SearchableModels\SearchableProductModel;

class SearchableProductRepository
{
    public function match($filter, $from, $size)
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

    public function matchAll($from, $size)
    {
        $params = [
            'index' => SearchableProductModel::INDEX_NAME,
            'from' => $from,
            'size' => $size
        ];

        return SearchableModel::client()->search($params['hits']['hits']);
    }
}
