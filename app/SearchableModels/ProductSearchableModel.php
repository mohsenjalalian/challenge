<?php

namespace App\SearchableModels;

class ProductSearchableModel extends SearchableModel
{
    public const INDEX_NAME = 'products';
    public const TYPE_NAME = '_doc';
    public const PAGE_SIZE = 2;

    // Here you can specify a mapping for model fields
    private $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256
                    ]
                ]
            ],
            'description' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256
                    ]
                ]
            ],
            'categories' => [
                'type' => 'text',
                'fields' => [
                    'keyword' => [
                        'type' => 'keyword',
                        'ignore_above' => 256
                    ]
                ]
            ],
            'price' => [
                'type' => 'long'
            ],
            'count' => [
                'type' => 'integer'
            ],
        ]
    ];

    /**
     * @return \array[][]
     */
    public function getMapping()
    {
        return $this->mapping;
    }
}
