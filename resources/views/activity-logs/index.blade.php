@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Staff Activity Log</h1>
            <p class="page-subtitle">Track product changes, price changes, purchases, payments, and staff actions.</p>
        </div>
    </div>

    <section class="app-card">
        <div class="app-card-header">
            <h2 class="app-card-title">Recent Activity</h2>
            <span class="badge text-bg-light border">{{ $logs->total() }} records</span>
        </div>
        @if($logs->isEmpty())
            <div class="empty-state">No activity has been recorded yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover app-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Staff</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                            <tr>
                                <td class="small text-muted">{{ $log->created_at?->format('M d, Y H:i') }}</td>
                                <td class="fw-semibold">{{ $log->user?->name ?? 'System' }}</td>
                                <td>
                                    @php
                                        $actionLower = strtolower($log->action);
                                        $badgeClass = 'bg-slate-subtle text-slate border border-slate-subtle';
                                        if (str_contains($actionLower, 'create') || str_contains($actionLower, 'store') || str_contains($actionLower, 'add') || str_contains($actionLower, 'login') || str_contains($actionLower, 'checkout') || str_contains($actionLower, 'place')) {
                                            $badgeClass = 'bg-emerald-subtle text-emerald border border-emerald-subtle';
                                        } elseif (str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'change')) {
                                            $badgeClass = 'bg-blue-subtle text-blue border border-blue-subtle';
                                        } elseif (str_contains($actionLower, 'delete') || str_contains($actionLower, 'destroy') || str_contains($actionLower, 'remove') || str_contains($actionLower, 'cancel') || str_contains($actionLower, 'logout')) {
                                            $badgeClass = 'bg-rose-subtle text-rose border border-rose-subtle';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ strtoupper($log->action) }}</span>
                                </td>
                                <td>{{ $log->description }}</td>
                                <td class="small text-muted">
                                    @if($log->properties)
                                        <details>
                                            <summary>View</summary>
                                            <pre class="mb-0 mt-2 small">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                                        </details>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-3 pb-3">{{ $logs->links() }}</div>
        @endif
    </section>
@endsection
