<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BackupController extends Controller
{
    public function index()
    {
        return view('backup.index');
    }

    public function export(Request $request, string $type)
    {
        $date = Carbon::parse($request->query('date', now()))->toDateString();

        $csv = match ($type) {
            'orders' => $this->ordersCsv(),
            'customers' => $this->customersCsv(),
            'inventory' => $this->inventoryCsv(),
            'purchases' => $this->purchasesCsv(),
            'daily' => $this->dailyCsv($date),
            default => $this->productsCsv(),
        };

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $type . '-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    private function productsCsv(): string
    {
        $rows = [['ID', 'Name', 'Category', 'Price', 'Small Price', 'Medium Price', 'Large Price', 'Stock', 'Recipe Cost', 'Margin %']];

        Product::with('category', 'ingredients.inventoryItem')->orderBy('name')->get()->each(function (Product $product) use (&$rows) {
            $rows[] = [
                $product->id,
                $product->name,
                $product->category_name,
                $product->price,
                $product->small_price,
                $product->medium_price,
                $product->large_price,
                $product->stock,
                $product->recipeCost(),
                $product->profitMargin(),
            ];
        });

        return $this->csv($rows);
    }

    private function ordersCsv(): string
    {
        $rows = [['Date', 'Order Number', 'Customer', 'Order Type', 'Service', 'Status', 'Payment Method', 'Subtotal', 'Discount', 'Total']];

        Order::with('customer')->orderByDesc('created_at')->get()->each(function (Order $order) use (&$rows) {
            $rows[] = [
                $order->created_at?->format('Y-m-d H:i'),
                $order->display_order_number,
                $order->customer_name ?: $order->customer?->name,
                $order->order_type_label,
                $order->service_label,
                $order->status,
                $order->payment_method,
                $order->subtotal_amount,
                $order->discount_amount,
                $order->total_amount,
            ];
        });

        return $this->csv($rows);
    }

    private function customersCsv(): string
    {
        $rows = [['ID', 'Name', 'Phone', 'Email', 'Points', 'Total Spent', 'Visits', 'Last Order']];

        Customer::orderBy('name')->get()->each(function (Customer $customer) use (&$rows) {
            $rows[] = [
                $customer->id,
                $customer->name,
                $customer->phone,
                $customer->email,
                $customer->points_balance,
                $customer->total_spent,
                $customer->visits,
                $customer->last_order_at,
            ];
        });

        return $this->csv($rows);
    }

    private function inventoryCsv(): string
    {
        $rows = [['ID', 'Name', 'Unit', 'Quantity', 'Low Stock At', 'Unit Cost', 'Low Stock']];

        InventoryItem::orderBy('name')->get()->each(function (InventoryItem $item) use (&$rows) {
            $rows[] = [
                $item->id,
                $item->name,
                $item->unit,
                $item->quantity_on_hand,
                $item->low_stock_quantity,
                $item->unit_cost,
                $item->is_low_stock ? 'Yes' : 'No',
            ];
        });

        return $this->csv($rows);
    }

    private function purchasesCsv(): string
    {
        $rows = [['Date', 'Purchase ID', 'Supplier', 'Reference', 'Total', 'Created By']];

        Purchase::with('supplier', 'user')->orderByDesc('purchase_date')->get()->each(function (Purchase $purchase) use (&$rows) {
            $rows[] = [
                $purchase->purchase_date?->toDateString(),
                $purchase->id,
                $purchase->supplier?->name,
                $purchase->reference,
                $purchase->total_amount,
                $purchase->user?->name,
            ];
        });

        return $this->csv($rows);
    }

    private function dailyCsv(string $date): string
    {
        $start = Carbon::parse($date)->startOfDay();
        $end = $start->copy()->endOfDay();
        $orders = Order::whereBetween('created_at', [$start, $end])->with('items.product')->get();
        $rows = [['Daily Backup Date', $date], []];
        $rows[] = ['Metric', 'Value'];
        $rows[] = ['Total Orders', $orders->count()];
        $rows[] = ['Paid Revenue', $orders->where('status', 'paid')->sum('total_amount')];
        $rows[] = ['Discounts', $orders->sum('discount_amount')];
        $rows[] = [];
        $rows[] = ['Order', 'Status', 'Payment Method', 'Total'];

        foreach ($orders as $order) {
            $rows[] = [$order->display_order_number, $order->status, $order->payment_method, $order->total_amount];
        }

        return $this->csv($rows);
    }

    private function csv(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);

        return stream_get_contents($handle) ?: '';
    }
}
