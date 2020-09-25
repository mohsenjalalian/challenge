<?php

namespace App\Console\Commands\Indices;

use App\SearchableModels\SearchableProductModel;
use App\SearchableModels\SearchableModel;
use Illuminate\Console\Command;

class CreateProductsIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:products-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create products index in elasticsearch';

    private $mapping;

    /**
     * CreateProductsIndex constructor.
     * @param SearchableProductModel $productSearchableModel
     */
    public function __construct(SearchableProductModel $productSearchableModel)
    {
        parent::__construct();

        $this->mapping = $productSearchableModel->getMapping();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $createIndexParams = [
            'index' => SearchableProductModel::INDEX_NAME,
            'body' => [
                'mappings' => $this->mapping
            ]
        ];

        SearchableModel::client()->indices()->create($createIndexParams);
    }
}
