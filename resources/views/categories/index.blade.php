@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Categories</h1>
            <p class="page-subtitle">Manage the product categories used by the catalog and POS.</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> New Category
        </a>
    </div>

    <div class="app-card">
        @if($categories->isEmpty())
            <div class="empty-state">
                <i class="bi bi-tags" style="font-size: 3rem; color: #d1d5db;"></i>
                <p>No categories yet. <a href="{{ route('categories.create') }}">Create one</a></p>
            </div>
        @else
            <table class="table app-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Products</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-teal-subtle text-primary border border-teal-subtle px-2 py-1">
                                    {{ $category->products_count }} product{{ $category->products_count === 1 ? '' : 's' }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $category->created_at?->format('M d, Y') ?? '-' }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->user()->canDeleteBackOfficeRecords())
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline confirm-delete">
                                        @csrf
                                        @method('DELETE')
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

            @if($categories->hasPages())
                <div class="d-flex justify-content-center p-3 border-top">
                    {{ $categories->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
