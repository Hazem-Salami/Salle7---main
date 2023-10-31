<?php

namespace App\Services\Category;

use App\Models\Category;
use App\Services\BaseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryService extends BaseService
{
    /**
     *
     * @return Response
     */
    public function getRootCategories(): Response
    {
        $categories = Category::where('category_id', null)->paginate(\request('size'));

        return $this->customResponse(true, 'get Categories Success', $categories);
    }

    /**
     * @param Request
     * @return Response
     */
    public function getChildCategories(Request $request): Response
    {
        $category = $request->get('category');

        $categories = Category::where('category_id', $category->id)->paginate(\request('size'));

        return $this->customResponse(true, 'get Categories Success', $categories);
    }
}
