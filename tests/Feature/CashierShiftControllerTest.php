<?php

namespace Tests\Feature;

use App\Models\CashierShift;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CashierShiftControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_can_open_and_close_shift_with_expected_cash(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $otherCashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        $this->travelTo(Carbon::parse('2026-06-03 09:00:00'));

        $this->actingAs($cashier)
            ->post(route('cashier-shifts.store'), [
                'opening_cash' => 20,
                'notes' => 'Morning shift',
            ])
            ->assertRedirect(route('cashier-shifts.index'))
            ->assertSessionHas('success');

        $shift = CashierShift::where('user_id', $cashier->id)->firstOrFail();

        $this->travelTo(Carbon::parse('2026-06-03 10:00:00'));
        $this->paidOrder($cashier, 'cash', 5.00);
        $this->paidOrder($cashier, 'card', 9.00);
        $this->paidOrder($otherCashier, 'cash', 7.00);

        $this->travelTo(Carbon::parse('2026-06-03 17:00:00'));

        $this->actingAs($cashier)
            ->put(route('cashier-shifts.close', $shift), [
                'cash_in' => 10,
                'cash_out' => 3,
                'closing_cash' => 32,
                'notes' => 'Drawer balanced',
            ])
            ->assertRedirect(route('cashier-shifts.index'))
            ->assertSessionHas('success');

        $shift->refresh();

        $this->assertSame(CashierShift::STATUS_CLOSED, $shift->status);
        $this->assertSame(5.0, $shift->cash_sales_amount);
        $this->assertSame(32.0, $shift->expected_cash_amount);
        $this->assertSame(32.0, $shift->closing_cash);
        $this->assertSame(0.0, $shift->cash_difference);

        $this->travelBack();
    }

    public function test_cashier_cannot_open_second_shift_before_closing_current_shift(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);

        CashierShift::create([
            'user_id' => $cashier->id,
            'opened_at' => now(),
            'opening_cash' => 15,
        ]);

        $this->actingAs($cashier)
            ->post(route('cashier-shifts.store'), [
                'opening_cash' => 20,
            ])
            ->assertRedirect(route('cashier-shifts.index'))
            ->assertSessionHas('error', 'Close your current shift before opening another one.');

        $this->assertSame(1, CashierShift::where('user_id', $cashier->id)->count());
    }

    public function test_cashier_cannot_close_another_cashiers_shift(): void
    {
        $cashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $otherCashier = User::factory()->create(['role' => User::ROLE_CASHIER]);
        $shift = CashierShift::create([
            'user_id' => $otherCashier->id,
            'opened_at' => now(),
            'opening_cash' => 15,
        ]);

        $this->actingAs($cashier)
            ->put(route('cashier-shifts.close', $shift), [
                'closing_cash' => 15,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'You do not have permission to access that shift.');

        $this->assertTrue($shift->fresh()->isOpen());
    }

    private function paidOrder(User $cashier, string $method, float $amount): void
    {
        $order = Order::create([
            'user_id' => $cashier->id,
            'order_date' => now()->toDateString(),
            'subtotal_amount' => $amount,
            'total_amount' => $amount,
            'discount_amount' => 0,
            'status' => 'paid',
            'payment_method' => $method,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'provider' => $method,
            'payment_method' => $method,
            'transaction_id' => strtoupper($method).'-TEST-'.uniqid(),
            'amount' => $amount,
            'status' => 'paid',
            'verification_status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
