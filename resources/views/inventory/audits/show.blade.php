@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Stock Audit Details</h1>
            <p class="page-subtitle">Reconciliation results for inventory stock-take session on {{ $audit->audit_date->format('M d, Y') }}.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to History
            </a>
            <a href="{{ route('audits.create') }}" class="btn btn-primary">
                <i class="bi bi-clipboard2-check me-1"></i> New Audit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Audit Summary details -->
        <div class="col-lg-4">
            <section class="app-card p-4 h-100">
                <h2 class="h5 fw-bold mb-3">Session Summary</h2>
                
                <div class="mb-3">
                    <span class="text-muted small d-block uppercase font-bold">Audit Date</span>
                    <span class="fw-bold fs-5">{{ $audit->audit_date->format('l, F d, Y') }}</span>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block uppercase font-bold">Auditor</span>
                    <div class="fw-bold fs-5">{{ $audit->user?->name ?? 'System' }}</div>
                    <span class="small text-muted">{{ $audit->user?->email }}</span>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block uppercase font-bold">Net Financial Variance</span>
                    <div class="fw-bold fs-4 @if($audit->total_variance_cost < 0) text-danger @elseif($audit->total_variance_cost > 0) text-success @else text-muted @endif">
                        ${{ number_format($audit->total_variance_cost, 2) }}
                    </div>
                </div>

                @if($audit->notes)
                    <div class="mb-0">
                        <span class="text-muted small d-block uppercase font-bold">Notes</span>
                        <div class="bg-light p-3 border rounded mt-1" style="font-size: 0.9rem; white-space: pre-line;">{{ $audit->notes }}</div>
                    </div>
                @endif
            </section>
        </div>

        <!-- Audit Itemized List -->
        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <div>
                        <h2 class="app-card-title">Discrepancy Details</h2>
                        <p class="text-muted small mb-0">Item-by-item comparison of expected vs physical levels.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover app-table align-middle">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Expected</th>
                                <th class="text-center">Physical</th>
                                <th class="text-center">Variance</th>
                                <th class="text-end">Cost Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($audit->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->inventoryItem?->name ?? 'Deleted Item' }}</div>
                                        <span class="small text-muted">Unit Cost: ${{ number_format($item->unit_cost, 4) }} / {{ $item->inventoryItem?->unit ?? 'g' }}</span>
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ number_format($item->theoretical_quantity, 3) }}
                                        <span class="small">{{ $item->inventoryItem?->unit ?? 'g' }}</span>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        {{ number_format($item->physical_quantity, 3) }}
                                        <span class="small text-muted">{{ $item->inventoryItem?->unit ?? 'g' }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($item->variance_quantity < 0)
                                            <span class="fw-bold text-danger">{{ number_format($item->variance_quantity, 3) }}</span>
                                        @elseif($item->variance_quantity > 0)
                                            <span class="fw-bold text-success">+{{ number_format($item->variance_quantity, 3) }}</span>
                                        @else
                                            <span class="text-muted">0.000</span>
                                        @endif
                                        <span class="small text-muted">{{ $item->inventoryItem?->unit ?? 'g' }}</span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        @if($item->variance_cost < 0)
                                            <span class="text-danger">-${{ number_format(abs($item->variance_cost), 2) }}</span>
                                        @elseif($item->variance_cost > 0)
                                            <span class="text-success">+${{ number_format($item->variance_cost, 2) }}</span>
                                        @else
                                            <span class="text-muted">$0.00</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
@endsection
