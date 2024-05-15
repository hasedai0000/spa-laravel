<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * ユーザー登録
     * 
     * @param Request $request
     * @return JsonResponse Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        $json = [
            'data' => $user
        ];
        return response()->json($json, Response::HTTP_OK);
    }

    /**
     * ログイン処理
     * 
     * @param Request $request
     * @return JsonResponse Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::whereEmail($request->email)->first();
            $user->tokens()->delete();
            $token = $user->createToken("login:user{$user->id}")->plainTextToken;
            //ログインが成功した場合はトークンを返す
            return response()->json(['token' => $token], Response::HTTP_OK);
        }
        return response()->json('Can Not Login', Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
