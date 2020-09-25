<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkProduct;
use App\Jobs\ProcessBulkProduct;
use App\Repositories\SearchableProductRepository;
use App\SearchableModels\SearchableProductModel;
use Illuminate\ {
    Http\Request,
    Http\Response,
    Http\JsonResponse
};

class ProductController extends Controller
{
    private $searchableProductRepository;

    /**
     * ProductController constructor.
     * @param SearchableProductRepository $searchableProductRepository
     */
    public function __construct(SearchableProductRepository $searchableProductRepository)
    {
        $this->searchableProductRepository = $searchableProductRepository;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->get('page');
        $pageSize = SearchableProductModel::PAGE_SIZE;
        $filter = $request->get('filter');

        if (isset($filter)) {
            $results = $this->searchableProductRepository->match($filter, $page, $pageSize);
        } else {
            $results = $this->searchableProductRepository->matchAll($page, $pageSize);
        }

        $results = manualPagination($results, $page, $pageSize);

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
