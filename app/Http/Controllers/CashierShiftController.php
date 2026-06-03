<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CashierShiftController extends Controller
{
    public function index(): View
    {
        $openShift = CashierShift::where('user_id', Auth::id())
            ->where('status', CashierShift::STATUS_OPEN)
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        $shifts = CashierShift::with('user')
            ->when(! Auth::user()->canManageBackOffice(), fn ($query) => $query->where('user_id', Auth::id()))
            ->orderBy('opened_at', 'desc')
            ->paginate(12);

        $currentCashSales = $openShift?->cashSalesAmount() ?? 0;
        $currentExpectedCash = $openShift?->expectedCash($currentCashSales) ?? 0;

        return view('cashier-shifts.index', compact(
            'openShift',
            'shifts',
            'currentCashSales',
            'currentExpectedCash'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $hasOpenShift = CashierShift::where('user_id', Auth::id())
            ->where('status', CashierShift::STATUS_OPEN)
            ->whereNull('closed_at')
            ->exists();

        if ($hasOpenShift) {
            return redirect()->route('cashier-shifts.index')
                ->with('error', 'Close your current shift before opening another one.');
        }

        CashierShift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'opening_cash' => (float) $data['opening_cash'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('cashier-shifts.index')
            ->with('success', 'Shift opened successfully.');
    }

    public function close(Request $request, CashierShift $cashierShift): RedirectResponse
    {
        if (! $this->canUseShift($cashierShift)) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access that shift.');
        }

        if (! $cashierShift->isOpen()) {
            return redirect()->route('cashier-shifts.index')
                ->with('error', 'This shift is already closed.');
        }

        $data = $request->validate([
            'cash_in' => ['nullable', 'numeric', 'min:0'],
            'cash_out' => ['nullable', 'numeric', 'min:0'],
            'closing_cash' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $closedAt = now();
        $cashIn = (float) ($data['cash_in'] ?? 0);
        $cashOut = (float) ($data['cash_out'] ?? 0);
        $closingCash = (float) $data['closing_cash'];

        $cashierShift->forceFill([
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
        ]);

        $cashSalesAmount = $cashierShift->cashSalesAmount($closedAt);
        $expectedCash = $cashierShift->expectedCash($cashSalesAmount);

        $cashierShift->update([
            'closed_at' => $closedAt,
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'cash_sales_amount' => $cashSalesAmount,
            'expected_cash_amount' => $expectedCash,
            'closing_cash' => $closingCash,
            'cash_difference' => round($closingCash - $expectedCash, 2),
            'status' => CashierShift::STATUS_CLOSED,
            'notes' => $data['notes'] ?? $cashierShift->notes,
        ]);

        return redirect()->route('cashier-shifts.index')
            ->with('success', 'Shift closed successfully.');
    }

    private function canUseShift(CashierShift $cashierShift): bool
    {
        return (string) $cashierShift->user_id === (string) Auth::id()
            || Auth::user()->canManageBackOffice();
    }
}
