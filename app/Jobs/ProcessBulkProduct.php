<?php

namespace App\Jobs;

use App\Models\Product;
use Carbon\Carbon;
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

        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::READ_CSV);

        $products = [];
        foreach ($file as $key => $row) {
            if ($file->key() != 0 && $file->valid()) {
                $products['name'] = $row[0];
                $products['price'] = $row[1];
                $products['description'] = $row[2];
                $products['count'] = $row[4];
                $products['created_at'] = Carbon::now();
                $products['updated_at'] = Carbon::now();
            }
        }

        if (!empty($products)) {
            Product::insert($products);
        }
    }
}
