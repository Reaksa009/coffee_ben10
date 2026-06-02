<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;

class LoyaltyService
{
    public const POINTS_PER_DOLLAR = 10;
    public const POINT_VALUE = 0.01;
    private const MIN_PAYABLE_TOTAL = 0.01;

    public function normalizePhone(?string $phone): ?string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return null;
        }

        $phone = preg_replace('/[^\d+]/', '', $phone) ?: '';

        return $phone === '' ? null : $phone;
    }

    public function findByPhone(?string $phone): ?Customer
    {
        $phone = $this->normalizePhone($phone);

        return $phone ? Customer::where('phone', $phone)->first() : null;
    }

    public function findOrCreateCustomer(?string $name, ?string $phone): ?Customer
    {
        $phone = $this->normalizePhone($phone);

        if (! $phone) {
            return null;
        }

        $customer = Customer::firstOrCreate(
            ['phone' => $phone],
            ['name' => trim((string) $name) ?: null]
        );

        if ($name && $customer->name !== trim($name)) {
            $customer->name = $name;
            $customer->save();
        }

        return $customer;
    }

    public function redeemableDiscount(?Customer $customer, float $eligibleAmount): float
    {
        if (! $customer || $eligibleAmount <= self::MIN_PAYABLE_TOTAL) {
            return 0.0;
        }

        $balanceValue = max(0, (int) $customer->points_balance) * self::POINT_VALUE;
        $maxDiscount = max(0, $eligibleAmount - self::MIN_PAYABLE_TOTAL);

        return round(min($balanceValue, $maxDiscount), 2);
    }

    public function redemptionFor(?Customer $customer, float $eligibleAmount, bool $requested): array
    {
        if (! $requested || ! $customer) {
            return ['points' => 0, 'discount' => 0.0];
        }

        $discount = $this->redeemableDiscount($customer, $eligibleAmount);
        $points = (int) floor($discount / self::POINT_VALUE);

        return [
            'points' => min($points, max(0, (int) $customer->points_balance)),
            'discount' => round($points * self::POINT_VALUE, 2),
        ];
    }

    public function redeem(Customer $customer, int $points): void
    {
        if ($points <= 0) {
            return;
        }

        $customer->points_balance = max(0, (int) $customer->points_balance - $points);
        $customer->total_points_redeemed = (int) $customer->total_points_redeemed + $points;
        $customer->save();
    }

    public function awardForPaidOrder(?Order $order): array
    {
        if (! $order || ! $order->customer_id || $order->loyalty_awarded_at) {
            return ['points' => (int) ($order?->loyalty_points_earned ?? 0), 'awarded' => false];
        }

        $customer = Customer::find($order->customer_id);
        if (! $customer) {
            return ['points' => 0, 'awarded' => false];
        }

        $points = max(0, (int) floor((float) $order->total_amount * self::POINTS_PER_DOLLAR));

        $customer->points_balance = (int) $customer->points_balance + $points;
        $customer->total_points_earned = (int) $customer->total_points_earned + $points;
        $customer->total_spent = (float) $customer->total_spent + (float) $order->total_amount;
        $customer->visits = (int) $customer->visits + 1;
        $customer->last_order_at = now();
        $customer->save();

        $order->loyalty_points_earned = $points;
        $order->loyalty_awarded_at = now();
        $order->save();

        return ['points' => $points, 'awarded' => true];
    }
}
