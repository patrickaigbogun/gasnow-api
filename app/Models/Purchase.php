<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cylinder_size_id',
        'purchase_kg_id',
        'invoice_no',
        'delivery_address',
        'delivery_time_id',
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cylinderSize(): BelongsTo
    {
        return $this->belongsTo(CylinderSize::class);
    }

    public function purchaseKg(): BelongsTo
    {
        return $this->belongsTo(PurchaseKg::class);
    }

    public function deliveryTime(): BelongsTo
    {
        return $this->belongsTo(DeliveryTime::class);
    }
}
