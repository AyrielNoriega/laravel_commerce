<?php
namespace App\Services;

use App\Exceptions\OrderNotFoundException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class OrderService
{
    public function getAllOrders()
    {
        return Order::with('products')->get();
    }


    public function createOrder($data)
    {
        try {
            //total de la orden
            $total = collect($data->products)->sum(function ($product) {
                $productModel = Product::findOrFail($product['product_id']);
                return $productModel->price * $product['quantity'];
            });

            $order = Order::create([
                'user_id' => $data->user_id,
                'total' => $total,
                'status' => $data->status,
            ]);

            // Asociar los productos a la orden
            foreach ($data->products as $product) {
                $productModel = Product::findOrFail($product['product_id']);
                $order->products()->attach($product['product_id'], [
                    'quantity' => $product['quantity'],
                    'price' => $productModel->price,
                ]);
            }

            return $order->load('products');
        } catch (ModelNotFoundException $e) {
            if ($e->getModel() === Product::class) {
                Log::error("Product not found: {$e->getMessage()}");
                throw new ProductNotFoundException();
            }
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception('An error occurred');
        }
    }


    public function getOrderById($id)
    {
        try {
            $order = Order::with('products')->findOrFail($id);
            return $order;
        } catch (ModelNotFoundException $e) {
            Log::error("Order not found: {$e->getMessage()}");
            throw new OrderNotFoundException();
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception('An error occurred');
        }
    }


    public function updateOrder($id, $data)
    {
        try {
            $order = Order::findOrFail($id);

            $order->update($data->only(['status']));

            // Calcular el nuevo total de la orden
            $total = collect($data->products)->sum(function ($product) {
                $productModel = Product::findOrFail($product['product_id']);
                return $productModel->price * $product['quantity'];
            });

            $order->update(['total' => $total]);

            // Sincronizar los productos asociados a la orden
            $order->products()->detach();
            foreach ($data->products as $product) {
                $productModel = Product::findOrFail($product['product_id']);
                $order->products()->attach($productModel->id, [
                    'quantity' => $product['quantity'],
                    'price' => $productModel->price,
                ]);
            }

            return $order->load('products');
        } catch (ModelNotFoundException $e) {
            Log::error("Order or Product not found: {$e->getMessage()}");
            throw new ModelNotFoundException("Order or Product not found: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception("An error occurred: {$e->getMessage()}");
        }
    }


    public function deleteOrder(int $id)
    {
        try {
            $order = Order::findOrFail($id);
            Log::error("An error occurred: {$order}");
            $order->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            Log::error("Order or Product not found: {$e->getMessage()}");
            throw new ModelNotFoundException("Order not found: {$e->getMessage()}");
        } catch (\Exception $e) {
            Log::error("An error occurred: {$e->getMessage()}");
            throw new \Exception("An error occurred: {$e->getMessage()}");
        }
    }
}
