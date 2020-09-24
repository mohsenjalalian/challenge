<?php

namespace App\SearchableModels;

use Elasticsearch\Client;

class SearchableModel
{
    /**
     * @return Client
     */
    public static function client()
    {
        return resolve('elastic');
    }
}
