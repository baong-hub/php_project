<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLopHocRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        // Form web: checkbox bỏ tick thì không được gửi lên -> coi là 0.
        // API (JSON): chỉ chuẩn hóa khi client có gửi, thiếu thì báo lỗi thay vì ngầm tắt.
        if (!$this->is('api/*') || $this->has('trang_thai')) {
            $this->merge([
                'trang_thai' => $this->boolean('trang_thai'),
            ]);
        }

        // Giảng viên chỉ tạo/sửa lớp do chính mình chủ nhiệm, không gán cho người khác
        if ($this->user() && !$this->user()->isAdmin()) {
            $this->merge(['giang_vien_id' => $this->user()->id]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ten_lop' => 'required|string|max:255',
            'ma_lop' => 'required|string|max:20|unique:lop_hocs,ma_lop',
            'giang_vien_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', User::ROLE_GIANG_VIEN),
            ],
            'so_dien_thoai_gvcn' => 'nullable|string|max:20',
            'ghi_chu' => 'nullable|string|max:500',
            'si_so' => 'required|integer|min:1',
            'trang_thai' => 'required|boolean',
        ];
    }

    /**
     * Thông báo lỗi tiếng Việt.
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute không được để trống.',
            'string' => ':attribute phải là chuỗi ký tự.',
            'max' => ':attribute không được vượt quá :max ký tự.',
            'integer' => ':attribute phải là số nguyên.',
            'boolean' => ':attribute không hợp lệ.',
            'ma_lop.unique' => 'Mã lớp này đã tồn tại.',
            'giang_vien_id.required' => 'Vui lòng chọn giảng viên chủ nhiệm.',
            'giang_vien_id.exists' => 'Giảng viên chủ nhiệm không hợp lệ.',
            'si_so.min' => 'Sĩ số phải từ :min trở lên.',
        ];
    }

    /**
     * Tên hiển thị của các trường trong thông báo lỗi.
     */
    public function attributes(): array
    {
        return [
            'ten_lop' => 'Tên lớp',
            'ma_lop' => 'Mã lớp',
            'giang_vien_id' => 'Giảng viên chủ nhiệm',
            'so_dien_thoai_gvcn' => 'Số điện thoại GVCN',
            'ghi_chu' => 'Ghi chú',
            'si_so' => 'Sĩ số',
            'trang_thai' => 'Trạng thái',
        ];
    }
}
