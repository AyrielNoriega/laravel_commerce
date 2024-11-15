<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductCollection;
use App\Models\Product;
use Illuminate\Http\Request;


/**
 * @OA\Info(
 *     title="API E-commerce",
 *     version="1.0.0",
 *     description="API E-commerce",
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor local",
 * )
 *
 */

class ProductController extends Controller
{

    /**
     * @OA\Get(
     *     tags={"products"},
     *     path="/api/v1/products",
     *     summary="Get list of products",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Product"))
     *     )
     * )
     */
    public function index()
    {
        return new ProductCollection(Product::all());
    }


    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create a product",
     *     tags={"products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "user_id"},
     *             ref="#/components/schemas/StoreProductRequest"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->all());
        return $product;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     summary="Get a product by ID",
     *     tags={"products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function show(Product $product)
    {
        $product = new ProductResource($product);
        return  $product;
    }


    /**
     * @OA\Patch(
     *     path="/api/v1/products/{product}",
     *     summary="Update a product",
     *     tags={"products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->update($request->all());
        return new ProductResource($product);
    }


    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     summary="Delete a product",
     *     tags={"products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Product deleted successfully",
     *     )
     * )
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
