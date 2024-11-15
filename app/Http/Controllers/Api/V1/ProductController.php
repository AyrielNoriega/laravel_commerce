<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\ProductNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Http\Resources\V1\ProductCollection;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

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
        $products = $this->productService->getAllProducts();
        return new ProductCollection($products);
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
        $product = $this->productService->createProduct($request->all());
        return $product;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/products/{product_id}",
     *     summary="Get a product by ID",
     *     tags={"products"},
     *     @OA\Parameter(
     *         name="product_id",
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
    public function show(int $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            return  new ProductResource($product);
        } catch (ProductNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
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
    public function update(UpdateProductRequest $request, int $id)
    {
        try {
            $product = $this->productService->updateProduct($id, $request->all());
            return new ProductResource($product);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "{$e->getMessage()}"], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => "{$e->getMessage()}"], 500);
        }
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
    public function destroy(int $id)
    {

        try {
            return $this->productService->deleteProduct($id);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
