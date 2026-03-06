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
            'description' => $this->description,
            'outlet_type' => $this->type_outlet_id ? (string) $this->type_outlet_id : null,
            'type_outlet_id' => $this->type_outlet_id,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo' => $this->logo,
            'image_url' => $this->image_url,
            'google_map_url' => $this->google_map_url,
            'url_deeplink' => $this->url_deeplink,
            'status' => $this->status,

            // Schedule information
            'schedule_mode' => $this->schedule_mode,
            'schedule_days' => $this->schedule_days,
            'schedule_start_time' => $this->schedule_start_time,
            'schedule_end_time' => $this->schedule_end_time,
            'schedule_start_date' => $this->schedule_start_date,
            'schedule_end_date' => $this->schedule_end_date,
            'schedule_status' => $this->schedule_status,

            // Computed fields
            'is_open' => $this->when($this->schedule_status !== null, fn() => $this->isCurrentlyOpen()),

            // Counts (loaded conditionally)
            'products_count' => $this->whenCounted('products'),

            // Relationships (loaded conditionally)
            'type_outlet' => $this->whenLoaded('typeOutlet', function () {
                return [
                    'id' => $this->typeOutlet->id,
                    'name' => $this->typeOutlet->name_type,
                ];
            }),

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Check if outlet is currently open based on schedule.
     */
    protected function isCurrentlyOpen(): bool
    {
        if ($this->schedule_status !== 'active') {
            return false;
        }

        $now = now();
        $currentDay = strtolower($now->format('l'));

        // Check if current day is in schedule_days
        $scheduleDays = is_array($this->schedule_days)
            ? $this->schedule_days
            : json_decode($this->schedule_days ?? '[]', true);

        if (!empty($scheduleDays) && !in_array($currentDay, $scheduleDays)) {
            return false;
        }

        // Check time range
        if ($this->schedule_start_time && $this->schedule_end_time) {
            $startTime = \Carbon\Carbon::parse($this->schedule_start_time);
            $endTime = \Carbon\Carbon::parse($this->schedule_end_time);
            $currentTime = $now->format('H:i:s');

            return $currentTime >= $startTime->format('H:i:s')
                && $currentTime <= $endTime->format('H:i:s');
        }

        return true;
    }
}
