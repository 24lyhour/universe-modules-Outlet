<?php

namespace Modules\Outlet\Models;

use App\Models\Company;
use App\Traits\HasUuid;
use App\Traits\IsTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Menu\Models\Menu;
use Modules\Order\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Order\Models\OutletReview;
use Modules\Outlet\Database\Factories\OutletFactory;
use Modules\Product\Models\Brand;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductType;

class Outlet extends Model
{
    use HasFactory, IsTenant, HasUuid, SoftDeletes;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'tenant_type',
        'tenant_id',
        'company_id',
        'description',
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
        'latitude',
        'longitude',
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
     * Relation to products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relation to menus.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Relation to company.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation to type outlet.
     */
    public function typeOutlet(): BelongsTo
    {
        return $this->belongsTo(TypeOutlet::class);
    }

    /**
     * Relation to product types (outlet HAS MANY product types).
     */
    public function productTypes(): HasMany
    {
        return $this->hasMany(ProductType::class);
    }

    /**
     * Relation to brands.
     */
    public function brands(): HasMany
    {
        return $this->hasMany(Brand::class);
    }

    /**
     * Relation to carts.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Relation to orders.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation to outlet reviews.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(OutletReview::class)->withoutGlobalScopes();
    }
}
