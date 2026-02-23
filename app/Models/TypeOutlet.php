<?php

namespace Modules\Outlet\Models;

use App\Traits\BelongsToOutlet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Outlet\Database\Factories\TypeOutletFactory;

class TypeOutlet extends Model
{
    use HasFactory, BelongsToOutlet;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [

            'uuid',
            'name_type',
            'outlet_id',
            'description',
            'is_active',
            'created_by',
            'updated_by',
            'deleted_by',
            'created_at',
            'updated_at',
            'deleted_at',
    ];

    protected static function newFactory(): TypeOutletFactory
    {
        return TypeOutletFactory::new();
    }

    /**
     * protected
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * relation to the user who created the type outlet
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * relation to the user who updated the type outlet
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * relation to the user who deleted the type outlet
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * reslateion to the outlets
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
