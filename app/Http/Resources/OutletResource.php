<?php

namespace Modules\Outlet\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'outlet_type' => $this->outlet_type,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo' => $this->logo,
            'image_url' => $this->image_url,
            'google_map_url' => $this->google_map_url,
            'url_deeplink' => $this->url_deeplink,
            'status' => $this->status,
            'schedule_mode' => $this->schedule_mode,
            'schedule_days' => $this->schedule_days,
            'schedule_start_time' => $this->schedule_start_time,
            'schedule_end_time' => $this->schedule_end_time,
            'schedule_start_date' => $this->schedule_start_date,
            'schedule_end_date' => $this->schedule_end_date,
            'schedule_status' => $this->schedule_status,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
