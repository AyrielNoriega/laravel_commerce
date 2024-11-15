<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\OrderNotFoundException;
use App\Exceptions\ProductNotFoundException;
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
        $orders = $this->orderService->getAllOrders();
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

        try {
            $order = $this->orderService->createOrder($request);
            return new OrderResource($order);
        } catch (ProductNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
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
            $order = $this->orderService->getOrderById($id);
            return new OrderResource($order);
        } catch (OrderNotFoundException $e) {
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
     *     ),
     * 
     * @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
     */
    public function destroy(int $id)
    {
        try {
            $this->orderService->deleteOrder($id);
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }
}
