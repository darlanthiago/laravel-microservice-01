<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    protected $repository;

    public function __construct(Category $category)
    {
        $this->repository = $category;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->repository->all();

        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUpdateCategory $request)
    {

        $category = $this->repository->create($request->validated());

        return new CategoryResource($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function show($url)
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUpdateCategory $request, $url)
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        $category->update($request->validated());

        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $url
     * @return \Illuminate\Http\Response
     */
    public function destroy($url)
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        $category->delete();

        return response(null, 204);

    }
}
