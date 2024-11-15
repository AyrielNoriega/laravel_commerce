<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\V1\OrderCollection;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class OrderController extends Controller
{

    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    /**
     * @OA\Get(
     *     tags={"orders"},
     *     path="/api/v1/orders",
     *     summary="Get list of orders",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/OrderResponse"))
     *     )
     * )
     */
    public function index()
    {
        $orders = Order::with('products')->get();
        return new OrderCollection($orders);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/orders",
     *     summary="Create a orders",
     *     tags={"orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             ref="#/components/schemas/CreateOrderRequest"
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Order")
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {

        // Calcular el total de la orden
        $total = collect($request->products)->sum(function ($product) {
            $productModel = Product::findOrFail($product['product_id']);
            return $productModel->price * $product['quantity'];
        });

        $order = Order::create([
            'user_id' => $request->user_id,
            'total' => $total,
            'status' => $request->status,
        ]);

        // Asociar los productos a la orden
        foreach ($request->products as $product) {
            $productModel = Product::findOrFail($product['product_id']);
            $order->products()->attach($product['product_id'], [
                'quantity' => $product['quantity'],
                'price' => $productModel->price,
            ]);
        }

        $p = new OrderResource($order->load('products'));

        return $p;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/orders/{order}",
     *     summary="Get a order by ID",
     *     tags={"orders"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResponse")
     *     )
     * )
     */
    public function show(int $id)
    {
        try {
            $order = Order::with('products')->findOrFail($id);
            return new OrderResource($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    /**
     * @OA\Patch(
     *     path="/api/v1/orders/{order}",
     *     summary="Update an order",
     *     tags={"orders"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOrderRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Order updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/OrderResponse")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function update(UpdateOrderRequest $request, int $id)
    {
        try {
            $order = $this->orderService->updateOrder($id, $request);
            return new OrderResource($order);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => "{$e->getMessage()}"], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => "{$e->getMessage()}"], 500);
        }
    }


    /**
     * @OA\Delete(
     *     path="/api/v1/orders/{order}",
     *     summary="Delete a order",
     *     tags={"orders"},
     *     @OA\Parameter(
     *         name="order",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Order deleted successfully",
     *     )
     * )
     */
    public function destroy(int $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
