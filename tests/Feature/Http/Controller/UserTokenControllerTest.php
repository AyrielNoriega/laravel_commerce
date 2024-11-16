<?php
namespace Tests\Feature\Http\Controller;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_get_token_with_valid_credentials()
    {
        // Crear un usuario
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Asegúrate de que la contraseña esté hasheada
        ]);

        // Datos de la solicitud
        $data = [
            'email' => 'test@example.com',
            'password' => 'password',
        ];

        // Hacer una solicitud POST a la ruta /sanctum/token
        $response = $this->postJson('/api/v1/token', $data);

        // Verificar que la respuesta tenga un código de estado 200
        $response->assertStatus(200);

        // Verificar que la respuesta tenga el token
        $response->assertJsonStructure([
            'token',
        ]);

        // Verificar que el token sea válido
        $this->assertNotNull($response->json('token'));
    }

    public function test_user_cannot_get_token_with_invalid_credentials()
    {
        // Crear un usuario
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'), // Asegúrate de que la contraseña esté hasheada
        ]);

        // Datos de la solicitud con una contraseña incorrecta
        $data = [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ];

        // Hacer una solicitud POST a la ruta /sanctum/token
        $response = $this->postJson('/api/v1/token', $data);

        // Verificar que la respuesta tenga un código de estado 422
        $response->assertStatus(422);

        // Verificar que la respuesta tenga el mensaje de error
        $response->assertJsonValidationErrors('email');
    }
}