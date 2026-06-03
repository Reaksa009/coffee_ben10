@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Supplier Info</h1>
            <p class="page-subtitle">Manage vendors for beans, milk, cups, lids, sugar, syrup, and packaging.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the highlighted fields.</div>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <section class="app-card p-4">
                <h2 class="h5 fw-bold mb-3">Add Supplier</h2>
                <form method="POST" action="{{ route('suppliers.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Supplier Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_name" value="{{ old('contact_name') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-save me-1"></i> Save Supplier
                    </button>
                </form>
            </section>
        </div>

        <div class="col-lg-8">
            <section class="app-card">
                <div class="app-card-header">
                    <h2 class="app-card-title">Suppliers</h2>
                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-basket-fill me-1"></i> New Purchase
                    </a>
                </div>
                @if($suppliers->isEmpty())
                    <div class="empty-state">No suppliers yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover app-table">
                            <thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Contact</th>
                                    <th>Purchases</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $supplier)
                                    <tr>
                                        <td>
                                            <form id="supplier-update-{{ $supplier->id }}" method="POST" action="{{ route('suppliers.update', $supplier) }}">
                                                @csrf
                                                @method('PUT')
                                            </form>
                                            <input form="supplier-update-{{ $supplier->id }}" type="text" name="name" class="form-control form-control-sm fw-semibold" value="{{ $supplier->name }}" required>
                                            <textarea form="supplier-update-{{ $supplier->id }}" name="address" class="form-control form-control-sm mt-1" rows="1" placeholder="Address">{{ $supplier->address }}</textarea>
                                            <textarea form="supplier-update-{{ $supplier->id }}" name="notes" class="form-control form-control-sm mt-1" rows="1" placeholder="Notes">{{ $supplier->notes }}</textarea>
                                        </td>
                                        <td style="min-width: 220px;">
                                            <input form="supplier-update-{{ $supplier->id }}" type="text" name="contact_name" class="form-control form-control-sm mb-1" value="{{ $supplier->contact_name }}" placeholder="Contact">
                                            <input form="supplier-update-{{ $supplier->id }}" type="text" name="phone" class="form-control form-control-sm mb-1" value="{{ $supplier->phone }}" placeholder="Phone">
                                            <input form="supplier-update-{{ $supplier->id }}" type="email" name="email" class="form-control form-control-sm" value="{{ $supplier->email }}" placeholder="Email">
                                        </td>
                                        <td>{{ $supplier->purchases_count }}</td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button form="supplier-update-{{ $supplier->id }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-save"></i>
                                                </button>
                                                @if(auth()->user()->canDeleteBackOfficeRecords())
                                                    <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="confirm-delete">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-3 pb-3">{{ $suppliers->links() }}</div>
                @endif
            </section>
        </div>
    </div>
@endsection
