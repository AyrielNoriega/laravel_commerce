<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class UserTokenController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/token",
     *     summary="Create a new token",
     *     tags={"tokens"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password'  => 'required',
        ]);

        $user = User::where('email', $request->get('email'))->first();


        if (!($user instanceof User)
            || !Hash::check($request->password, $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => 'El email no existe o no coincide con nuestros registros',
            ]);

        }

        return response()->json([
            'token' => $user->createToken($request->email)->plainTextToken,
        ]);
    }
}
