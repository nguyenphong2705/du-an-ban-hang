<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $rawCode = strtoupper(trim((string) $this->input('coupon_code', '')));
        $tokens = preg_split('/[\s,;+|]+/', $rawCode, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $this->merge([
            'coupon_code' => $rawCode,
            'coupon_tokens_count' => count($tokens),
        ]);
    }

    public function rules(): array
    {
        return [
            'coupon_code' => ['required', 'string', 'max:50'],
            'coupon_tokens_count' => ['integer', 'max:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'coupon_code.required' => 'Vui lòng nhập hoặc chọn 01 mã giảm giá.',
            'coupon_tokens_count.max' => 'Mỗi đơn hàng chỉ được áp dụng duy nhất 01 mã giảm giá (không thể áp dụng 2 mã cùng lúc).',
        ];
    }
}
