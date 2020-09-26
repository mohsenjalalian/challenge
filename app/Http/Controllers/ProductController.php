<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkProduct;
use App\Http\Responses\IndexProduct;
use App\Jobs\ProcessBulkProduct;
use App\Repositories\SearchableProductRepository;
use App\SearchableModels\SearchableProductModel;
use Illuminate\{Http\Request, Http\Response, Http\JsonResponse, Http\UploadedFile};

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
     * @param IndexProduct $indexProductResponse
     * @return JsonResponse
     */
    public function index(Request $request, IndexProduct $indexProductResponse)
    {
        $page = $request->get('page');
        $pageSize = SearchableProductModel::PAGE_SIZE;
        $filter = $request->get('filter');

        if (isset($filter)) {
            $results = $this->searchableProductRepository->match($filter, $page, $pageSize);
        } else {
            $results = $this->searchableProductRepository->matchAll($page, $pageSize);
        }

	    $response = $indexProductResponse->response($results);

        $responseWithPagination = manualPagination($response, $page, $pageSize);

        return response()->json($responseWithPagination, Response::HTTP_OK);
    }

    /**
     * @param BulkProduct $request
     * @return JsonResponse
     */
    public function bulk(BulkProduct $request)
    {
        $validatedData = $request->validated();

        /** @var UploadedFile $productsFile */
        $productsFile = $validatedData['products'];

        $fileName = uniqid(). '.' .$productsFile->clientExtension();
        $productsFile->storeAs('products', $fileName);

        ProcessBulkProduct::dispatch($fileName)->onQueue(ProcessBulkProduct::QUEUE_NAME);

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
