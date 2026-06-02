<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $allCustomers = Customer::query()
            ->orderByDesc('last_order_at')
            ->orderByDesc('created_at')
            ->get()
            ->when($search, function ($customers) use ($search) {
                $needle = strtolower($search);

                return $customers->filter(function ($customer) use ($needle) {
                    return str_contains(strtolower((string) $customer->name), $needle)
                        || str_contains(strtolower((string) $customer->phone), $needle);
                });
            })
            ->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $customers = new LengthAwarePaginator(
            $allCustomers->forPage($page, $perPage),
            $allCustomers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $summary = [
            'total' => Customer::count(),
            'points' => Customer::sum('points_balance'),
            'spent' => Customer::sum('total_spent'),
            'visits' => Customer::sum('visits'),
        ];

        return view('customers.index', compact('customers', 'summary', 'search'));
    }

    public function show(Customer $customer): View
    {
        $orders = Order::with('items.product', 'payments')
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('customers.show', compact('customer', 'orders'));
    }
}
