<?php

namespace App\Models;

use App\Enums\FeeTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentChannel extends Model
{
    use HasFactory;
    protected $fillable = [
        'payment_method_id',
        'name',
        'code',
        'icon',
        'minimum_amount',
        'maximum_amount',
        'fee_type',
        'fee_amount',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'minimum_amount' => 'decimal:2',
            'maximum_amount' => 'decimal:2',
            'fee_amount' => 'decimal:2',
            'fee_type' => FeeTypeEnum::class,
            'status' => StatusEnum::class,
        ];
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', StatusEnum::Active);
    }
}
