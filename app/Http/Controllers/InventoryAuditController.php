<?php

namespace App\Http\Controllers;

use App\Models\InventoryAudit;
use App\Models\InventoryAuditItem;
use App\Models\InventoryItem;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryAuditController extends Controller
{
    public function index(): View
    {
        $audits = InventoryAudit::with('user')
            ->orderBy('audit_date', 'desc')
            ->paginate(15);

        $summary = [
            'total_audits' => InventoryAudit::count(),
            'total_variance' => InventoryAudit::sum('total_variance_cost'),
        ];

        return view('inventory.audits.index', compact('audits', 'summary'));
    }

    public function create(): View
    {
        $items = InventoryItem::orderBy('name')->get();
        return view('inventory.audits.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'audit_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array'],
            'items.*.inventory_item_id' => ['required', 'exists:inventory_items,id'],
            'items.*.physical_quantity' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $audit = DB::transaction(function () use ($data) {
                $totalVarianceCost = 0;
                $auditItemsData = [];

                foreach ($data['items'] as $itemInput) {
                    $item = InventoryItem::findOrFail($itemInput['inventory_item_id']);
                    $theoretical = (float) $item->quantity_on_hand;
                    $physical = (float) $itemInput['physical_quantity'];
                    $variance = $physical - $theoretical;
                    $unitCost = (float) $item->unit_cost;
                    $varianceCost = $variance * $unitCost;

                    $totalVarianceCost += $varianceCost;

                    $auditItemsData[] = [
                        'inventory_item_id' => $item->id,
                        'theoretical_quantity' => $theoretical,
                        'physical_quantity' => $physical,
                        'variance_quantity' => $variance,
                        'unit_cost' => $unitCost,
                        'variance_cost' => $varianceCost,
                    ];

                    // Reconcile physical inventory stock on hand
                    $item->update([
                        'quantity_on_hand' => $physical,
                    ]);
                }

                $audit = InventoryAudit::create([
                    'user_id' => Auth::id(),
                    'audit_date' => $data['audit_date'],
                    'notes' => $data['notes'] ?? null,
                    'total_variance_cost' => $totalVarianceCost,
                ]);

                foreach ($auditItemsData as $itemData) {
                    $itemData['inventory_audit_id'] = $audit->id;
                    InventoryAuditItem::create($itemData);
                }

                ActivityLogger::log(
                    'inventory.audit',
                    "Performed stock audit on " . $audit->audit_date->format('Y-m-d') . " with variance cost of $" . number_format($audit->total_variance_cost, 2),
                    $audit,
                    ['total_variance_cost' => $audit->total_variance_cost]
                );

                return $audit;
            });

            return redirect()
                ->route('audits.show', $audit->id)
                ->with('success', 'Inventory audit recorded and stock levels reconciled successfully.');
        } catch (\Throwable $e) {
            report($e);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error creating inventory audit: ' . $e->getMessage());
        }
    }

    public function show(InventoryAudit $audit): View
    {
        $audit->load('items.inventoryItem', 'user');
        return view('inventory.audits.show', compact('audit'));
    }
}
