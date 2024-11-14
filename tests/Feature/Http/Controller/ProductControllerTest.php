<?php

namespace Tests\Feature\Http\Controller;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get(): void
    {
        User::factory()->count(5)->create();
        Product::factory()->count(5)->create();
        $response = $this->getJson('/api/v1/products');
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJsonCount(5, 'data');
    }


    public function test_create_new_product()
    {

        $user = User::factory()->create();

        $data = [
            'title' => 'Nuevo producto',
            'description' => 'It is a long established fact that a reader will be distracted.',
            'price' => 1000,
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/v1/products', $data);

        $response->assertStatus(201);
        $response->assertHeader('content-type', 'application/json');
        $this->assertDatabaseHas('products', $data);
    }


    public function test_show_product()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/products/{$product->getKey()}");

        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
        $response->assertJson([
            'data' => [
                'id' => $product->id,
                'title' => $product->title,
                'description' => $product->description,
                'price' => $product->price,
            ]
        ]);
    }


    public function test_update_product()
    {

        $product = Product::factory()->create();
        
        $data = [
            'title' => 'Nuevo producto',
            'description' => 'It is a long established fact that a reader will be distracted.',
            'price' => 20000,
        ];

        $response = $this->patchJson("/api/v1/products/{$product->getKey()}", $data);
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
    }


    public function test_delete_product()
    {

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/v1/products/{$product->getKey()}");

        $response->assertSuccessful();
        $response->assertHeader('content-type', 'application/json');
        // $response->assertDeleted($product);
    }
}
