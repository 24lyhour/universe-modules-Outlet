<?php

namespace Modules\Outlet\Http\Requests\Dashboard\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutletRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'outlet_type' => ['required', 'exists:type_outlets,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'google_map_url' => ['nullable', 'url', 'max:500'],
            'url_deeplink' => ['nullable', 'string', 'max:500'],
            'status' => ['required', 'in:active,inactive'],
            'schedule_mode' => ['nullable', 'string'],
            'schedule_days' => ['nullable', 'string'],
            'schedule_start_time' => ['nullable', 'string'],
            'schedule_end_time' => ['nullable', 'string'],
            'schedule_start_date' => ['nullable', 'date'],
            'schedule_end_date' => ['nullable', 'date'],
            'schedule_status' => ['nullable', 'string'],
        ];
    }
}
