@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Promo Codes</h1>
            <p class="page-subtitle">Manage discount codes and promotional offers</p>
        </div>
        <a href="{{ route('promos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Promo
        </a>
    </div>

    <div class="app-card">
        @if($promos->isEmpty())
            <div class="empty-state">
                <i class="bi bi-tag" style="font-size: 3rem; color: #d1d5db;"></i>
                <p>No promo codes yet. <a href="{{ route('promos.create') }}">Create one</a></p>
            </div>
        @else
            <table class="table app-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Usage</th>
                        <th>Valid</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($promos as $promo)
                        <tr>
                            <td>
                                <strong>{{ $promo->code }}</strong>
                                @if($promo->description)
                                    <div class="text-muted small">{{ $promo->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($promo->discount_type === 'percentage')
                                    <span class="badge text-bg-info">{{ $promo->discount_value }}%</span>
                                @else
                                    <span class="badge text-bg-info">${{ $promo->discount_value }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $promo->times_used }}
                                @if($promo->usage_limit)
                                    / {{ $promo->usage_limit }}
                                @endif
                            </td>
                            <td>
                                @if($promo->valid_from)
                                    <div class="small">{{ $promo->valid_from->format('M d, Y') }}</div>
                                @endif
                                @if($promo->valid_until)
                                    <div class="small text-muted">to {{ $promo->valid_until->format('M d, Y') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($promo->active)
                                    <span class="badge text-bg-success">Active</span>
                                @else
                                    <span class="badge text-bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('promos.edit', $promo) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->canDeleteBackOfficeRecords())
                                    <form action="{{ route('promos.destroy', $promo) }}" method="POST" class="d-inline confirm-delete">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($promos->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $promos->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
