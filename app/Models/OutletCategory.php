<?php

namespace Modules\Outlet\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Outlet\Database\Factories\OutletCategoryFactory;

class OutletCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'outlet_id',
        'outlet_category_id',
        'outlet_category_name',
        'parent_name',
        'description',
        'status',
    ];

    /**
     * protection the data type
     */
    public  $case = [
        'status' => 'boolean',
    ];

    /**
     * protctio factory database
     */
    protected static function newFactory(): OutletCategoryFactory
    {
        return OutletCategoryFactory::new();
    }

    /**
     * relation to the outlet
     */
    public function Outlet() : hasMany
    {
        return $this->hasMany(Outlet::class, 'outlet_id');
        
    }

    /**
     * scope the global outlet
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

}
