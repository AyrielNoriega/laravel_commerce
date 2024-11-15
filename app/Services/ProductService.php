<?php
namespace App\Services;

use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class ProductService
{
    public function getAllProducts()
    {
        return Product::all();
    }


    public function createProduct(array $data)
    {
        try {
            $product = Product::create($data);
            return response()->json($product, 201);
        } catch (\Exception $e) {
            Log::error("An error occurred while creating the product: {$e->getMessage()}");
            return response()->json(['error' => 'An error occurred while creating the product'], 500);
        }
    }


    public function getProductById(int $id)
    {
        try {
            $product = Product::findOrFail($id);
            return $product;
        } catch (ModelNotFoundException $e) {
            Log::error("Product not found: {$e->getMessage()}");
            throw new ProductNotFoundException();
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception('An error occurred');
        }
    }


    public function updateProduct(int $id, array $data)
    {
        try {
            $product = Product::findOrFail($id);
            $product->update($data);

            return $product;
        } catch (ModelNotFoundException $e) {
            Log::error("Product or Product not found: {$e->getMessage()}");
            throw new ModelNotFoundException("Product or Product not found: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception("An error occurred: {$e->getMessage()}");
        }
    }


    public function deleteProduct(int $id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            Log::error("Product or Product not found: {$e->getMessage()}");
            throw new ModelNotFoundException("Product not found: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception("An error occurred: {$e->getMessage()}");
        }
    }
}
