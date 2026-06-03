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
                                <td><span class="badge text-bg-light border">{{ $log->action }}</span></td>
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
