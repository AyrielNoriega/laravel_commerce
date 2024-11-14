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

}
