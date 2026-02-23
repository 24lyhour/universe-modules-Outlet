<?php

namespace Modules\Outlet\Models;

use App\Traits\IsTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Outlet\Database\Factories\OutletFactory;

class Outlet extends Model
{
    use HasFactory, IsTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'tenant_type',
        'tenant_id',
        'company_id',
        'name',
        'address',
        'phone',
        'type_outlet_id',
        'outlet_id',
        'image_url',
        'menu_id',
        'product_id',
        'email',
        'logo',
        'google_map_url',
        'url_deeplink',
        'status',
        'schedule_mode',
        'schedule_days',
        'schedule_start_time',
        'schedule_end_time',
        'schedule_start_date',
        'schedule_end_date',
        'schedule_status',
        'created_by',
        'updated_by',
    ];


    protected static function newFactory(): OutletFactory
    {
        return OutletFactory::new();
    }

    /**
     * resltion to the product
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * relation to the menu
     */
    public function menu()
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * RELATION TO THE COMPANY
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation to the type outlet
     */
    public function typeOutlet()
    {
        return $this->belongsTo(TypeOutlet::class);
    }
}
