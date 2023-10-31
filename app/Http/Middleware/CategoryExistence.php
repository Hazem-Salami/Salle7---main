<?php

namespace App\Http\Middleware;

use App\Http\Traits\ResponseTrait;
use App\Models\Category;
use Closure;
use Illuminate\Http\Request;

class CategoryExistence
{
    use ResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response | \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $category = Category::where('id', $request->id)->first();

        if($category) {

            $request->attributes->add(['category' => $category]);

            return $next($request);

        } else{
            return $this->customResponse(false, 'category Not Found', null, 400);
        }
    }
}
