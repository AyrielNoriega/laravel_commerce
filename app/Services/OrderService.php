<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;


class OrderService
{
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
}