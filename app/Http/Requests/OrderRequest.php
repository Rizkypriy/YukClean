<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Semua user yang login bisa membuat order
    }

    public function rules(): array
    {
        return [
            'service_id' => 'required_without:bundle_id|exists:services,id',
            'bundle_id' => 'required_without:service_id|exists:bundles,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'floor_count' => 'nullable|integer|min:1',
            'room_size' => 'nullable|string|max:100',
            'special_conditions' => 'nullable|string',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string',
            'promo_code' => 'nullable|string|exists:promos,code',
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required_without' => 'Pilih layanan yang ingin dipesan',
            'bundle_id.required_without' => 'Pilih paket yang ingin dipesan',
            'booking_date.after_or_equal' => 'Tanggal booking tidak boleh kurang dari hari ini',
            'end_time.after' => 'Jam selesai harus setelah jam mulai',
        ];
    }
}