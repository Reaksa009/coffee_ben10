<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::query()
            ->withCount('purchases')
            ->orderBy('name')
            ->paginate(15);

        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $supplier = Supplier::create($this->validated($request));

        ActivityLogger::log('supplier.created', 'Created supplier ' . $supplier->name, $supplier);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request));

        ActivityLogger::log('supplier.updated', 'Updated supplier ' . $supplier->name, $supplier);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->purchases()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'This supplier has purchase history and cannot be deleted.');
        }

        $name = $supplier->name;
        $supplier->delete();

        ActivityLogger::log('supplier.deleted', 'Deleted supplier ' . $name);

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
