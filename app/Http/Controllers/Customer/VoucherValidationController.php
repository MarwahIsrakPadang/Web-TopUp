<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherValidationController extends Controller
{
    public function validate(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'amount' => ['required', 'numeric', 'min:0'],
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher || !$voucher->isValid()) {
            return back()->withErrors(['code' => 'Voucher tidak valid atau sudah kedaluwarsa.']);
        }

        if ($request->amount < $voucher->minimum_order) {
            return back()->withErrors([
                'code' => 'Minimal belanja Rp ' . number_format($voucher->minimum_order, 0, ',', '.') . ' untuk menggunakan voucher ini.',
            ]);
        }

        $discount = $voucher->type === 'percentage'
            ? round($request->amount * $voucher->amount / 100)
            : min($voucher->amount, $request->amount);

        return back()->with('discount', $discount);
    }
}
