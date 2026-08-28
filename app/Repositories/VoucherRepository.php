<?php

namespace App\Repositories;

use App\Models\Voucher;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class VoucherRepository
{
    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return Cache::remember("vouchers_page_{$perPage}", 3600, fn() =>
            Voucher::orderBy('created_at', 'desc')->paginate($perPage)
        );
    }

    public function findByCode(string $code): ?Voucher
    {
        return Voucher::where('code', strtoupper($code))->first();
    }

    public function findById(int $id): ?Voucher
    {
        return Voucher::find($id);
    }

    public function findByIdOrFail(int $id): Voucher
    {
        return Voucher::findOrFail($id);
    }

    public function create(array $data): Voucher
    {
        $voucher = Voucher::create($data);

        Cache::forget('vouchers_page_10');

        return $voucher;
    }

    public function update(Voucher $voucher, array $data): bool
    {
        $result = $voucher->update($data);

        Cache::forget('vouchers_page_10');

        return $result;
    }

    public function delete(Voucher $voucher): ?bool
    {
        $result = $voucher->delete();

        Cache::forget('vouchers_page_10');

        return $result;
    }
}