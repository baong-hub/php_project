<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/login  {email, password} -> token dùng cho header "Authorization: Bearer <token>"
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (!$user || !Hash::check($request->validated('password'), $user->password)) {
            return $this->error('Email hoặc mật khẩu không đúng.', 401);
        }

        if (!$user->trang_thai) {
            return $this->error('Tài khoản của bạn đã bị khóa.', 403);
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->userData($user),
        ], 'Đăng nhập thành công');
    }

    /**
     * GET /api/user
     */
    public function me(Request $request)
    {
        return $this->success($this->userData($request->user()));
    }

    /**
     * POST /api/logout — thu hồi token đang dùng
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Đã đăng xuất');
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'role_text' => $user->tenVaiTro(),
        ];
    }
}
