<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Product;
use App\SearchableModels\ProductSearchableModel;
use SplFileObject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBulkProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $fileName;

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
        $file = 'app/products/'.$this->fileName;
        $path = storage_path($file);

        $file = new SplFileObject($path);
        $file->setFlags(SplFileObject::READ_CSV);

        foreach ($file as $key => $row) {
            $categoryIds = [];
            if ($file->key() != 0 && $file->valid()) {
                $categoryTitles = explode(',', $row[3]);
                foreach ($categoryTitles as $categoryTitle) {
                    $category = Category::where('title', $categoryTitle);

                    if (!$category instanceof Category) {
                        $category = Category::create([
                            'title' => $categoryTitle
                        ]);
                    }

                    $categoryIds[] = $category->id;
                }
                $product = Product::create(
                    [
                        'name' => $row[0],
                        'price' => $row[1],
                        'description' => $row[2],
                        'count' => $row[4],
                    ]
                );

                $product->categories()->attach($categoryIds);


                $searchableProducts['body'][] = [
                    'index' => [
                        '_index' => ProductSearchableModel::INDEX_NAME,
                        '_type' => ProductSearchableModel::TYPE_NAME
                    ]
                ];

                $searchableProducts['body'][] = [
                    'name'     => $row[0],
                    'price' => $row[1],
                    'description' => $row[2],
                    'count' => $row[4],
                    'categories' => $categoryTitles
                ];

                ProductSearchableModel::client()->bulk($searchableProducts);
            }
        }
    }
}
