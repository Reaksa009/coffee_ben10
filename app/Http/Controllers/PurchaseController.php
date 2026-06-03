<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $purchases = Purchase::query()
            ->with('supplier', 'user')
            ->orderByDesc('purchase_date')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::query()->orderBy('name')->get();
        $inventoryItems = InventoryItem::query()->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'inventoryItems'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'reference' => ['nullable', 'string', 'max:120'],
            'purchase_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array'],
            'items.*.inventory_item_id' => ['nullable', 'exists:inventory_items,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = collect($data['items'])
            ->filter(fn ($item) => ! empty($item['inventory_item_id']) && (float) ($item['quantity'] ?? 0) > 0)
            ->map(function ($item) {
                $quantity = (float) $item['quantity'];
                $unitCost = (float) ($item['unit_cost'] ?? 0);

                return [
                    'inventory_item_id' => $item['inventory_item_id'],
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'line_total' => round($quantity * $unitCost, 2),
                ];
            })
            ->values();

        if ($items->isEmpty()) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Add at least one purchase item.');
        }

        $purchase = DB::transaction(function () use ($data, $items) {
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'] ?? null,
                'user_id' => Auth::id(),
                'reference' => $data['reference'] ?? null,
                'purchase_date' => $data['purchase_date'],
                'total_amount' => $items->sum('line_total'),
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $itemData) {
                $purchase->items()->create($itemData);
                $this->restockInventory($itemData);
            }

            return $purchase;
        });

        ActivityLogger::log('purchase.created', 'Recorded purchase #' . $purchase->id, $purchase, [
            'total_amount' => $purchase->total_amount,
        ]);

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase recorded and inventory restocked.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('supplier', 'user', 'items.inventoryItem');

        return view('purchases.show', compact('purchase'));
    }

    private function restockInventory(array $itemData): void
    {
        $inventoryItem = InventoryItem::findOrFail($itemData['inventory_item_id']);
        $oldQuantity = (float) $inventoryItem->quantity_on_hand;
        $oldCost = (float) $inventoryItem->unit_cost;
        $addedQuantity = (float) $itemData['quantity'];
        $addedCost = (float) $itemData['unit_cost'];
        $newQuantity = $oldQuantity + $addedQuantity;

        $inventoryItem->quantity_on_hand = $newQuantity;
        $inventoryItem->unit_cost = $newQuantity > 0
            ? round((($oldQuantity * $oldCost) + ($addedQuantity * $addedCost)) / $newQuantity, 4)
            : $addedCost;
        $inventoryItem->save();
    }
}
