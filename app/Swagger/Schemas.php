<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="title", type="string", example="Product 1", description="Title of the product"),
 *     @OA\Property(property="description", type="string", example="Description of the product", description="Description of the product"),
 *     @OA\Property(property="price", type="number", example=1000, description="Price of the product"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID of the user who created the product"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 *
 * @OA\Schema(
 *     schema="StoreProductRequest",
 *     type="object",
 *     required={"title", "description", "price", "user_id"},
 *     @OA\Property(property="title", type="string", example="Product 1", description="Title of the product"),
 *     @OA\Property(property="description", type="string", example="Description of the product", description="Description of the product"),
 *     @OA\Property(property="price", type="number", example=1000, description="Price of the product"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID of the user who created the product")
 * )
 *
 *
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     type="object",
 *     @OA\Property(property="title", type="string", example="Updated Product", description="Title of the product"),
 *     @OA\Property(property="image", type="string", example="<url>", description="Image of the product"),
 *     @OA\Property(property="description", type="string", example="Updated description", description="Description of the product"),
 *     @OA\Property(property="price", type="number", example=1500, description="Price of the product"),
 *     @OA\Property(property="user_id", type="integer", example=1, description="ID of the user who created the product")
 * )
 *
 *
 */


class Schemas {}