<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('id')),
            ],
            // Để trống = giữ nguyên mật khẩu cũ
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ]);
    }

    /**
     * Admin không được tự hạ quyền hoặc tự khóa tài khoản của chính mình
     * (tránh trường hợp hệ thống không còn admin nào).
     */
    public function after(): array
    {
        return [
            function ($validator) {
                if ((int) $this->route('id') !== $this->user()->id) {
                    return;
                }

                if ($this->input('role') !== $this->user()->role) {
                    $validator->errors()->add('role', 'Bạn không thể tự thay đổi vai trò của chính mình.');
                }

                if (!$this->boolean('trang_thai')) {
                    $validator->errors()->add('trang_thai', 'Bạn không thể tự khóa tài khoản của chính mình.');
                }
            },
        ];
    }
}
