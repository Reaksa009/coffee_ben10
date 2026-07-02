@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Shift Closing</h1>
            <p class="page-subtitle">Open a cashier shift, track cash sales, and close the drawer.</p>
        </div>
        <a href="{{ route('pos.index') }}" class="btn btn-primary">
            <i class="bi bi-cup-hot me-1"></i> New Sale
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-5">
            <div class="app-card p-4 h-100">
                @if($openShift)
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                        <div>
                            <h2 class="app-card-title">Current Shift</h2>
                            <div class="text-muted small">Opened {{ $openShift->opened_at->format('M d, Y H:i') }}</div>
                        </div>
                        <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle">Open</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Opening Cash</div>
                                <div class="fw-bold">${{ number_format($openShift->opening_cash, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small">Cash Sales</div>
                                <div class="fw-bold">${{ number_format($currentCashSales, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="text-muted small">Expected Cash Right Now</div>
                                <div class="fs-4 fw-bold text-primary">${{ number_format($currentExpectedCash, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('cashier-shifts.close', $openShift) }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cash In</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="cash_in"
                                        value="{{ old('cash_in', 0) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Cash Out</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" min="0" name="cash_out"
                                        value="{{ old('cash_out', 0) }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Counted Closing Cash</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="closing_cash"
                                    value="{{ old('closing_cash') }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Closing Note</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes', $openShift->notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 mt-4">
                            <i class="bi bi-lock-fill me-1"></i> Close Shift
                        </button>
                    </form>
                @else
                    <h2 class="app-card-title mb-3">Open Shift</h2>
                    <form method="POST" action="{{ route('cashier-shifts.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Opening Cash</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" name="opening_cash"
                                    value="{{ old('opening_cash', 0) }}" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Opening Note</label>
                            <textarea name="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-unlock-fill me-1"></i> Open Shift
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="col-xl-7">
            <div class="app-card h-100">
                <div class="app-card-header">
                    <div>
                        <h2 class="app-card-title">Recent Shifts</h2>
                        <div class="text-muted small">Managers see all staff shifts. Cashiers see their own.</div>
                    </div>
                </div>

                @if($shifts->isEmpty())
                    <div class="empty-state">No shifts recorded yet.</div>
                @else
                    <table class="table app-table">
                        <thead>
                            <tr>
                                <th>Cashier</th>
                                <th>Opened</th>
                                <th>Status</th>
                                <th>Expected</th>
                                <th>Counted</th>
                                <th>Difference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $shift)
                                <tr>
                                    <td>{{ $shift->user?->name ?? 'Unknown' }}</td>
                                    <td>
                                        <div>{{ $shift->opened_at->format('M d, H:i') }}</div>
                                        @if($shift->closed_at)
                                            <div class="text-muted small">Closed {{ $shift->closed_at->format('M d, H:i') }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shift->isOpen())
                                            <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle">
                                                Open
                                            </span>
                                        @else
                                            <span class="badge bg-slate-subtle text-slate border border-slate-subtle">
                                                Closed
                                            </span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($shift->isOpen() ? $shift->expectedCash() : $shift->expected_cash_amount, 2) }}</td>
                                    <td>{{ $shift->closing_cash === null ? '-' : '$' . number_format($shift->closing_cash, 2) }}</td>
                                    <td>
                                        @if($shift->isOpen())
                                            -
                                        @else
                                            <span class="{{ $shift->cash_difference < 0 ? 'text-danger' : ($shift->cash_difference > 0 ? 'text-success' : 'text-muted') }}">
                                                ${{ number_format($shift->cash_difference, 2) }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if($shifts->hasPages())
                        <div class="d-flex justify-content-center p-3 border-top">
                            {{ $shifts->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
