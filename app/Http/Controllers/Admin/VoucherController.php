<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VoucherRequest;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class VoucherController extends Controller
{
    public function __construct(
        private readonly VoucherService $voucherService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Vouchers/Index', [
            'vouchers' => $this->voucherService->paginate(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Vouchers/Form', [
            'voucher' => null,
        ]);
    }

    public function store(VoucherRequest $request): RedirectResponse
    {
        $this->voucherService->create($request->validated());

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function edit(Voucher $voucher): Response
    {
        return Inertia::render('Admin/Vouchers/Form', [
            'voucher' => $voucher,
        ]);
    }

    public function update(VoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $this->voucherService->update($voucher, $request->validated());

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $this->voucherService->delete($voucher);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Voucher berhasil dihapus.');
    }
}
