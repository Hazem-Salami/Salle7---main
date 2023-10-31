<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     *
     * @var ProductService
     */
    protected ProductService $productService;

    // singleton pattern, service container
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function getProductsCategory(Request $request): Response
    {
        return $this->productService->getProductsCategory($request);
    }

    public function getLastProduct(): Response
    {
        return $this->productService->getLastProduct();
    }

    public function filter(Request $request): Response
    {
        return $this->productService->filter($request);
    }
}
