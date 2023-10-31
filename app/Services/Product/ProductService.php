<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductService extends BaseService
{
    /**
     * @param Request
     * @return Response
     */
    public function getProductsCategory(Request $request): Response
    {
        $category = $request->get('category');

        $products = Product::where('category_id', $category->id)->paginate(\request('size'));;

        return $this->customResponse(true, 'تمت عملية الحصول على المنتجات بنجاح', $products);
    }

    public function getLastProduct(): Response
    {
        $products = Product::orderBy('updated_at', 'desc')
            ->paginate(\request('size'));

        return $this->customResponse(true, 'تمت عملية الحصول على أحدث المنتجات بنجاح', $products);
    }

    public function filter(Request $request): Response
    {
        $products = Product::join('storehouses', 'storehouses.id', '=', 'products.storehouse_id')
            ->where(function ($products) use ($request) {

                if($request->has('categoryId'))
                    $products->where('category_id', $request->get('categoryId'));

                if($request->has('fromPrice') && $request->has('toPrice'))
                    $products->whereBetween('price',[$request->get('fromPrice'), $request->get('toPrice')]);

                if($request->has('storehouseId'))
                    $products->where('storehouse_id', $request->get('storehouseId'));

            })->paginate(\request('size'));

        return $this->customResponse(true, 'تمت عملية فلترة المنتجات بنجاح', $products);
    }
}
