<?php

namespace App\Services;

use App\Models\Voucher;
use App\Repositories\VoucherRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class VoucherService
{
    public function __construct(
        private readonly VoucherRepository $repository
    ) {}

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findByIdOrFail(int $id): Voucher
    {
        return $this->repository->findByIdOrFail($id);
    }

    public function create(array $data): Voucher
    {
        $data['code'] = strtoupper($data['code']);

        return $this->repository->create($data);
    }

    public function update(Voucher $voucher, array $data): void
    {
        if (isset($data['code'])) {
            $data['code'] = strtoupper($data['code']);
        }

        $this->repository->update($voucher, $data);
    }

    public function delete(Voucher $voucher): void
    {
        $this->repository->delete($voucher);
    }
}
