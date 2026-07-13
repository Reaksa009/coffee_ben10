@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Perform Physical Stock Audit</h1>
            <p class="page-subtitle">Reconcile current system stock counts by inputting actual physical counts from the shelves.</p>
        </div>
        <div>
            <a href="{{ route('audits.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to History
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger mb-4">
            <div class="fw-bold mb-1">Validation Errors:</div>
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('audits.store') }}" class="needs-validation" novalidate>
        @csrf

        <div class="row g-4 mb-4">
            <div class="col-lg-4">
                <section class="app-card p-4">
                    <h2 class="h5 fw-bold mb-3"><i class="bi bi-info-circle me-1 text-primary"></i> Audit Details</h2>
                    
                    <div class="mb-3">
                        <label class="form-label">Audit Date</label>
                        <input type="date" name="audit_date" class="form-control" value="{{ old('audit_date', date('Y-m-d')) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes / Observations</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="E.g. Audited end of week. Found minor leakage in whole milk container.">{{ old('notes') }}</textarea>
                    </div>

                    <div class="alert alert-warning small mb-0 mt-4">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Warning:</strong> Saving this audit will instantly update the stock levels on your active inventory sheet to the physical counts entered.
                    </div>
                </section>
            </div>

            <div class="col-lg-8">
                <section class="app-card">
                    <div class="app-card-header">
                        <div>
                            <h2 class="app-card-title">Physical Counts Sheet</h2>
                            <p class="text-muted small mb-0">Input actual values. Unchanged fields will default to system quantity.</p>
                        </div>
                    </div>

                    @if($items->isEmpty())
                        <div class="empty-state py-5 text-center">
                            <i class="bi bi-inbox fs-1 text-muted opacity-25"></i>
                            <div class="mt-3 font-semibold text-muted">No inventory items available to audit.</div>
                            <a href="{{ route('inventory.index') }}" class="btn btn-primary mt-3 btn-sm">Add Inventory Items first</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover app-table align-middle">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th class="text-center" style="width: 140px;">System Qty</th>
                                        <th class="text-center" style="width: 180px;">Physical Count</th>
                                        <th class="text-center" style="width: 130px;">Variance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $index => $item)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $item->name }}</div>
                                                <span class="small text-muted">Unit Cost: ${{ number_format($item->unit_cost, 4) }} / {{ $item->unit }}</span>
                                                <input type="hidden" name="items[{{ $index }}][inventory_item_id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="text-center bg-light">
                                                <span class="fw-semibold text-muted" id="theoretical-qty-{{ $item->id }}">{{ number_format($item->quantity_on_hand, 3) }}</span>
                                                <span class="small text-muted">{{ $item->unit }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm justify-content-center">
                                                    <input type="number" step="0.001" min="0" 
                                                           name="items[{{ $index }}][physical_quantity]" 
                                                           id="physical-qty-{{ $item->id }}"
                                                           class="form-control text-center physical-input" 
                                                           data-item-id="{{ $item->id }}"
                                                           data-theoretical="{{ $item->quantity_on_hand }}"
                                                           data-unit="{{ $item->unit }}"
                                                           value="{{ old('items.'.$index.'.physical_quantity', $item->quantity_on_hand) }}" 
                                                           required>
                                                    <span class="input-group-text">{{ $item->unit }}</span>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold" id="variance-qty-{{ $item->id }}">0.000</span>
                                                <span class="small text-muted" id="variance-unit-{{ $item->id }}">{{ $item->unit }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="app-card-footer p-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('audits.index') }}" class="btn btn-light font-bold">Cancel</a>
                            <button type="submit" class="btn btn-primary font-bold">
                                <i class="bi bi-check-circle me-1"></i> Save Audit & Reconcile Stock
                            </button>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputs = document.querySelectorAll('.physical-input');

            function calculateVariance(input) {
                const itemId = input.dataset.itemId;
                const theoretical = parseFloat(input.dataset.theoretical);
                const physical = parseFloat(input.value) || 0;
                const variance = physical - theoretical;

                const varianceEl = document.getElementById(`variance-qty-${itemId}`);
                varianceEl.innerText = (variance >= 0 ? '+' : '') + variance.toFixed(3);

                if (variance < 0) {
                    varianceEl.className = 'fw-bold text-danger';
                } else if (variance > 0) {
                    varianceEl.className = 'fw-bold text-success';
                } else {
                    varianceEl.className = 'fw-bold text-muted';
                }
            }

            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    calculateVariance(this);
                });
                // Initial calculation
                calculateVariance(input);
            });
        });
    </script>
@endsection
