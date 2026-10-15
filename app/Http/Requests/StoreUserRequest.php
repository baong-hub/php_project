<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Checkbox không được gửi lên khi bỏ tick, nên chuẩn hóa về 0
     * để rule "required|boolean" không báo lỗi sai.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'trang_thai' => $this->boolean('trang_thai'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'trang_thai' => 'required|boolean',
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'email' => ':attribute không đúng định dạng.',
            'boolean' => ':attribute không hợp lệ.',
            'in' => ':attribute không hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min' => 'Mật khẩu phải có ít nhất :min ký tự.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Họ tên',
            'email' => 'Email',
            'role' => 'Vai trò',
            'trang_thai' => 'Trạng thái',
            'password' => 'Mật khẩu',
        ];
    }
}
