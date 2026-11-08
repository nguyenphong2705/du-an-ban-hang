<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Chuẩn hóa địa chỉ giao hàng từ các Select Option (Tỉnh/TP, Quận/Huyện, Phường/Xã) + Số nhà, tên đường
        $parts = array_filter([
            trim((string) $this->input('street_address', '')),
            trim((string) $this->input('ward', '')),
            trim((string) $this->input('district', '')),
            trim((string) $this->input('province', '')),
        ], static fn (string $part): bool => $part !== '');

        $fullAddress = count($parts) >= 4
            ? implode(', ', $parts)
            : trim((string) ($this->input('address') ?: implode(', ', $parts)));

        $this->merge([
            'customer_name' => strip_tags(trim((string) $this->input('customer_name', ''))),
            'phone' => preg_replace('/[^0-9+]/', '', (string) $this->input('phone', '')),
            'full_address' => strip_tags($fullAddress),
            'note' => $this->filled('note') ? strip_tags(trim((string) $this->input('note'))) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[0-9]{8,10}$/'],
            'province' => ['nullable', 'string', 'max:150'],
            'district' => ['nullable', 'string', 'max:150'],
            'ward' => ['nullable', 'string', 'max:150'],
            'street_address' => ['nullable', 'string', 'max:300'],
            'full_address' => ['required', 'string', 'max:500'],
            'shipping_mode' => ['nullable', 'string', Rule::in(['FLAT_RATE', 'ZONE_BASED'])],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', Rule::in(['COD', 'BANK_TRANSFER', 'MOMO_QR'])],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Vui lòng nhập họ và tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không hợp lệ (VD: 0909123456).',
            'full_address.required' => 'Vui lòng chọn Tỉnh/Thành phố, Quận/Huyện, Phường/Xã và nhập số nhà, tên đường.',
            'payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
        ];
    }
}
