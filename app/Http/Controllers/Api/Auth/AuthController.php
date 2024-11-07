<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AuthRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Garanta que o usuário foi criado antes de tentar gerar um token
        if ($user) {
            // Gere um token de acesso para o novo usuário
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Retorne o token junto com a mensagem de sucesso
            return response()->json([
                'message' => 'User created successfully',
                'token' => $token
            ]);
        } else {
            // Retorne uma resposta de erro se o usuário não pôde ser criado
            return response()->json(['message' => 'User registration failed'], 500);
        }
    }

    public function auth(AuthRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        //Logout others devices
        $user->tokens()->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            "message" => "logout successfull"
        ]);
    }
}
