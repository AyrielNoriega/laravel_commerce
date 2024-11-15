<?php

namespace Tests\Feature\Http\Controller;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        User::factory()->create();
        Order::factory()->count(5)->create();
        $response = $this->getJson('/api/v1/orders');
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonCount(5, 'data');
    }


    public function test_create_new_order()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();

        $orderData = [
            'user_id' => $user->id,
            'status' => 'pendiente',
            'products' => $products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ];
            })->toArray(),
        ];
        $response = $this->postJson('/api/v1/orders', $orderData);

        $response->assertStatus(201);
        $response->assertHeader('content-type', 'application/json');
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => 'pendiente',
        ]);
    }


    public function test_show_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/orders/{$order->getKey()}");

        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJson([
            'data' => [
                'id' => $order->id,
                'user_id' => $order->user_id,
                'total' => $order->total,
                'status' => $order->status,
                'created_at' => $order->created_at,
                'updated_at' => $order->updated_at,
            ]
        ]);
    }


    public function test_update_order()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(1)->create();
        $order = Order::factory()->create();


        $orderData = [
            'status' => 'pendiente',
            'products' => $products->map(function ($product) {
                return [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ];
            })->toArray(),
        ];

        $response = $this->patchJson("/api/v1/orders/{$order->getKey()}", $orderData);
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
    }


    public function test_delete_order()
    {
        User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->deleteJson("/api/v1/orders/{$order->getKey()}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('orders', ['id' => $order->id]);

    }
}
