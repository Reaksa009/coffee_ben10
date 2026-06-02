@extends('layouts.app')

@section('content')
    <div class="page-head">
        <div>
            <h1 class="page-title">Create Promo Code</h1>
            <p class="page-subtitle">Set up a new discount or promotional offer</p>
        </div>
    </div>

    <div class="app-card" style="max-width: 600px;">
        <form method="POST" action="{{ route('promos.store') }}">
            @csrf

            <div class="mb-3">
                <label for="code" class="form-label">Promo Code *</label>
                <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ old('code') }}" placeholder="SUMMER20" required>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                    placeholder="e.g., Summer promotion 20% off">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="discount_type" class="form-label">Discount Type *</label>
                    <select id="discount_type" name="discount_type" class="form-select @error('discount_type') is-invalid @enderror" required>
                        <option value="">Select type</option>
                        <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                    </select>
                    @error('discount_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="discount_value" class="form-label">Discount Value *</label>
                    <input type="number" id="discount_value" name="discount_value" class="form-control @error('discount_value') is-invalid @enderror"
                        value="{{ old('discount_value') }}" placeholder="20" step="0.01" min="0" required>
                    @error('discount_value')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="min_order_amount" class="form-label">Min Order Amount</label>
                    <input type="number" id="min_order_amount" name="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror"
                        value="{{ old('min_order_amount') }}" placeholder="0" step="0.01" min="0">
                    @error('min_order_amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="usage_limit" class="form-label">Usage Limit</label>
                    <input type="number" id="usage_limit" name="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror"
                        value="{{ old('usage_limit') }}" placeholder="Unlimited" min="1">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="valid_from" class="form-label">Valid From</label>
                    <input type="date" id="valid_from" name="valid_from" class="form-control @error('valid_from') is-invalid @enderror"
                        value="{{ old('valid_from') }}">
                    @error('valid_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="valid_until" class="form-label">Valid Until</label>
                    <input type="date" id="valid_until" name="valid_until" class="form-control @error('valid_until') is-invalid @enderror"
                        value="{{ old('valid_until') }}">
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input type="checkbox" id="active" name="active" class="form-check-input" value="1"
                        {{ old('active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> Create Promo
                </button>
                <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
