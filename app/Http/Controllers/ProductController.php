<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkProduct;
use App\Jobs\ProcessBulkProduct;
use App\SearchableModels\ProductSearchableModel;
use App\SearchableModels\SearchableModel;
use Illuminate\ {
    Pagination\Paginator,
    Http\Request,
    Http\Response,
    Http\JsonResponse
};

class ProductController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->get('page');
        $pageSize = ProductSearchableModel::PAGE_SIZE;
        $filter = $request->get('filter');

        if (isset($filter)) {
            $params = [
                'index' => 'products',
                'from' => $page,
                'size' =>  $pageSize,
                'body'  => [
                    'query' => [
                        'match' => $filter
                    ]
                ]
            ];
        } else {
            $params = [
                'index' => 'products',
                'from' => $page,
                'size' =>  $pageSize
            ];
        }

        $results = SearchableModel::client()->search($params)['hits']['hits'];

        $startingPoint = ($page * $pageSize) - $pageSize;

        $results = array_slice($results, $startingPoint, $pageSize, true);

        $results= new Paginator($results, $pageSize, $page);

        return response()->json($results, Response::HTTP_OK);
    }

    /**
     * @param BulkProduct $request
     * @return JsonResponse
     */
    public function bulk(BulkProduct $request)
    {
        /** @var Request $validatedRequest */
        $validatedRequest = $request->validated();

        $fileName = uniqid(). '.' .$validatedRequest->file('products')->clientExtension();
        $validatedRequest->file('products')->storeAs('products', $fileName);

        ProcessBulkProduct::dispatch($fileName)->onQueue('bulk_product');

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
