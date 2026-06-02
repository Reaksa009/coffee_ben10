<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PromoController extends Controller
{
    public function index(): View
    {
        $promos = Promo::orderBy('created_at', 'desc')->paginate(15);
        return view('promos.index', compact('promos'));
    }

    public function create(): View
    {
        return view('promos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promos,code'],
            'description' => ['nullable', 'string', 'max:500'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after:valid_from'],
            'active' => ['boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');

        Promo::create($data);

        return redirect()->route('promos.index')
            ->with('success', 'Promo created successfully.');
    }

    public function edit(Promo $promo): View
    {
        return view('promos.edit', compact('promo'));
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promos,code,' . $promo->id],
            'description' => ['nullable', 'string', 'max:500'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after:valid_from'],
            'active' => ['boolean'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['active'] = $request->boolean('active');

        $promo->update($data);

        return redirect()->route('promos.index')
            ->with('success', 'Promo updated successfully.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $promo->delete();
        return redirect()->route('promos.index')
            ->with('success', 'Promo deleted successfully.');
    }

    public function validate(Request $request)
    {
        $code = $request->query('code');
        $orderAmount = $request->query('amount', 0);

        $promo = Promo::where('code', strtoupper($code))->first();

        if (!$promo) {
            return response()->json([
                'valid' => false,
                'message' => 'Promo code not found.',
            ], 404);
        }

        if (!$promo->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Promo code is expired or no longer valid.',
            ], 422);
        }

        if ($promo->min_order_amount && $orderAmount < $promo->min_order_amount) {
            return response()->json([
                'valid' => false,
                'message' => "Minimum order amount is {$promo->min_order_amount}",
            ], 422);
        }

        $discount = $promo->calculateDiscount($orderAmount);

        return response()->json([
            'valid' => true,
            'promo_id' => $promo->id,
            'code' => $promo->code,
            'discount_type' => $promo->discount_type,
            'discount_value' => $promo->discount_value,
            'discount_amount' => round($discount, 2),
            'message' => "Discount of $" . round($discount, 2) . " applied!",
        ]);
    }
}
