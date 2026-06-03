<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $items = InventoryItem::query()
            ->withCount('productIngredients')
            ->orderBy('name')
            ->paginate(20);

        $lowStockItems = InventoryItem::query()
            ->orderBy('name')
            ->get()
            ->filter->is_low_stock;

        return view('inventory.index', compact('items', 'lowStockItems'));
    }

    public function store(Request $request)
    {
        $item = InventoryItem::create($this->validated($request));

        ActivityLogger::log('inventory.created', 'Created inventory item ' . $item->name, $item);

        return redirect()->route('inventory.index')->with('success', 'Inventory item created.');
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $inventory->update($this->validated($request));

        ActivityLogger::log('inventory.updated', 'Updated inventory item ' . $inventory->name, $inventory);

        return redirect()->route('inventory.index')->with('success', 'Inventory item updated.');
    }

    public function destroy(InventoryItem $inventory)
    {
        $name = $inventory->name;

        if ($inventory->productIngredients()->exists() || $inventory->purchaseItems()->exists()) {
            return redirect()->route('inventory.index')
                ->with('error', 'This item is used by recipes or purchases and cannot be deleted.');
        }

        $inventory->delete();

        ActivityLogger::log('inventory.deleted', 'Deleted inventory item ' . $name);

        return redirect()->route('inventory.index')->with('success', 'Inventory item deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'unit' => ['required', 'string', 'max:30'],
            'quantity_on_hand' => ['required', 'numeric', 'min:0'],
            'low_stock_quantity' => ['required', 'numeric', 'min:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
