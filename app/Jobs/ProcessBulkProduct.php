<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\SearchableModels\SearchableProductModel;
use SplFileObject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkProduct implements ShouldQueue
{
    public const QUEUE_NAME = 'bulk_product';

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fileName;

    private $productName;
    private $productPrice;
    private $productDescription;
    private $productCount;
    private $productCategoryTitles;
    private $productCategoryIds;

    /**
     * ProcessBulkProduct constructor.
     * @param $fileName
     */
    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Todo need refactor
        $file = 'app/products/'.$this->fileName;
        $path = storage_path($file);

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV);

        //Todo check for bulk insert

        foreach ($file as $key => $row) {
            if ($file->key() != 0 && $file->valid()) {

                $this->setProductRow($row);

                $categoryIds = $this->getCategoryIds($this->productCategoryTitles);

                $product = Product::create(
                    [
                        'name' => $this->productName,
                        'price' => $this->productPrice,
                        'description' => $this->productDescription,
                        'count' => $this->productCount,
                    ]
                );

                $product->categories()->attach($categoryIds);

                $searchableProducts['body'][] = [
                    'index' => [
                        '_index' => SearchableProductModel::INDEX_NAME,
                        '_type' => SearchableProductModel::TYPE_NAME
                    ]
                ];

                $searchableProducts['body'][] = [
                    'name'     => $this->productName,
                    'price' => $this->productPrice,
                    'description' => $this->productDescription,
                    'count' => $this->productCount,
                    'categories' => $this->productCategoryTitles
                ];

                SearchableProductModel::client()->bulk($searchableProducts);
            }
        }
    }

    /**
     * @param array $row
     */
    private function setProductRow(array $row)
    {
        $this->productCategoryTitles = explode(',', $row[3]);
        $this->productName = $row[0];
        $this->productPrice = $row[1];
        $this->productDescription = $row[2];
        $this->productCount = $row[4];
    }

    /**
     * @param array $productCategoryTitles
     * @return array
     */
    private function getCategoryIds(array $productCategoryTitles)
    {
        $categoryIds = [];
        foreach ($productCategoryTitles as $categoryTitle) {
            $category = Category::where('title', $categoryTitle);

            if (!$category instanceof Category) {
                $category = Category::create([
                    'title' => $categoryTitle
                ]);
            }

            $categoryIds[] = $category->id;
        }

        return $categoryIds;
    }
}
