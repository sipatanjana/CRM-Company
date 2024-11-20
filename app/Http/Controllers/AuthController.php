<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->failedFunction(['error' => 'Invalid credentials'], 400);
            }
        } catch (JWTException $e) {
            return $this->failedFunction(['error' => 'Could not create token'], 500);
        }

        return $this->respondWithToken($token);
    }

    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return $this->failedFunction(['user_not_found'], 404);
            } else {
                $model = User::where('id', Auth::user()->id)->get();
                return UserResource::collection($model);
            }
        } catch (TokenExpiredException $e) {
            return $this->failedFunction(['token_expired'], 401);
            return response()->json(['token_expired'], 401);
        } catch (TokenInvalidException $e) {
            return $this->failedFunction(['token_invalid'], 401);
            return response()->json(['token_invalid'], 401);
        } catch (JWTException $e) {
            return $this->failedFunction(['token_absent'], 401);
            return response()->json(['token_absent'], 401);
        }
    }

    public function logout()
    {
        Auth::logout(true);

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_at' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}
