@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Inventory Audits & Variance</h1>
            <p class="page-subtitle">Perform physical inventory stock counts, track variance discrepancies, and calculate wastage costs.</p>
        </div>
        <div>
            <a href="{{ route('audits.create') }}" class="btn btn-primary">
                <i class="bi bi-clipboard2-check me-1"></i> Perform Stock Audit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4 mb-4">
        <!-- Stats Total Audits -->
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="background-color: var(--brand); color: white; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="fw-bold" style="opacity: 0.85; font-size: 0.9rem; text-transform: uppercase;">Total Audits</span>
                        <i class="bi bi-calculator fs-4" style="opacity: 0.85;"></i>
                    </div>
                    <h3 class="fw-bold mb-0" style="font-size: 2rem;">{{ $summary['total_audits'] }}</h3>
                </div>
            </div>
        </div>

        <!-- Stats Total Variance Cost -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold" style="font-size: 0.9rem; text-transform: uppercase;">Net Audit Variance</span>
                        @if($summary['total_variance'] < 0)
                            <i class="bi bi-arrow-down-right-circle-fill text-danger fs-4"></i>
                        @else
                            <i class="bi bi-arrow-up-right-circle-fill text-success fs-4"></i>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-0 @if($summary['total_variance'] < 0) text-danger @else text-success @endif" style="font-size: 2rem;">
                        ${{ number_format($summary['total_variance'], 2) }}
                    </h3>
                    <span class="small text-muted">Negative indicates inventory shrinkage/wastage.</span>
                </div>
            </div>
        </div>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <div>
                <h2 class="app-card-title">Audit Sessions History</h2>
                <p class="text-muted small mb-0">List of physical stock reconciliations and net cost variances.</p>
            </div>
        </div>

        @if($audits->isEmpty())
            <div class="empty-state py-5 text-center">
                <i class="bi bi-journal-medical fs-1 text-muted opacity-25"></i>
                <div class="mt-3 font-semibold text-muted">No audit sessions recorded yet.</div>
                <div class="small text-muted mt-1">Audit physical stock to identify gaps between system levels and physical levels.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Audit Date</th>
                            <th>Performed By</th>
                            <th>Notes</th>
                            <th>Total Net Variance</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($audits as $audit)
                            <tr>
                                <td class="fw-bold text-teal" style="font-size: 1.05rem;">
                                    {{ $audit->audit_date->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $audit->user?->name ?? 'Unknown' }}</div>
                                    <span class="small text-muted">{{ $audit->user?->email }}</span>
                                </td>
                                <td style="max-width: 300px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">
                                    {{ $audit->notes ?: '-' }}
                                </td>
                                <td>
                                    <span class="badge @if($audit->total_variance_cost < 0) bg-danger-subtle text-danger @elseif($audit->total_variance_cost > 0) bg-success-subtle text-success @else bg-light text-muted @endif border">
                                        ${{ number_format($audit->total_variance_cost, 2) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('audits.show', $audit->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 pb-3">{{ $audits->links() }}</div>
        @endif
    </section>
@endsection
