<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\VoucherTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'amount',
        'minimum_order',
        'maximum_usage',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'minimum_order' => 'decimal:2',
            'type' => VoucherTypeEnum::class,
            'status' => StatusEnum::class,
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query
            ->where('status', StatusEnum::Active)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->whereNull('maximum_usage')
                    ->orWhereColumn('used_count', '<', 'maximum_usage');
            });
    }

    public function isValid(): bool
    {
        if ($this->status !== StatusEnum::Active) {
            return false;
        }

        if ($this->start_date > now() || $this->end_date < now()) {
            return false;
        }

        if ($this->maximum_usage !== null && $this->used_count >= $this->maximum_usage) {
            return false;
        }

        return true;
    }
}
