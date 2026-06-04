@php
    $promo = $promo ?? null;
    $method = strtoupper($method ?? 'POST');
    $discountType = old('discount_type', $promo?->discount_type ?? 'percentage');
    $activeValue = old('active', ($promo?->active ?? true) ? '1' : '0');
    $isActive = (string) $activeValue === '1';
    $timesUsed = $promo?->times_used ?? 0;
@endphp

@once
    <style>
        .promo-form-grid {
            align-items: start;
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr) 340px;
            max-width: 1120px;
        }

        .promo-form-card,
        .promo-summary-card {
            padding: 1.15rem;
        }

        .promo-form-section {
            border-bottom: 1px solid var(--line);
            margin-bottom: 1.15rem;
            padding-bottom: 1.15rem;
        }

        .promo-form-section:last-of-type {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .promo-section-title {
            align-items: center;
            display: flex;
            font-size: .95rem;
            font-weight: 800;
            gap: .5rem;
            margin-bottom: 1rem;
        }

        .promo-code-field {
            font-weight: 800;
            text-transform: uppercase;
        }

        .promo-segment-group {
            display: grid;
            gap: .65rem;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .promo-segment {
            align-items: center;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: .5rem;
            color: var(--ink);
            cursor: pointer;
            display: flex;
            gap: .65rem;
            min-height: 52px;
            padding: .75rem .85rem;
        }

        .promo-segment i {
            align-items: center;
            background: rgba(15, 118, 110, .1);
            border-radius: .5rem;
            color: var(--brand);
            display: inline-flex;
            flex: 0 0 auto;
            height: 34px;
            justify-content: center;
            width: 34px;
        }

        .btn-check:checked + .promo-segment {
            background: rgba(15, 118, 110, .08);
            border-color: var(--brand);
            box-shadow: inset 0 0 0 1px rgba(15, 118, 110, .22);
            color: var(--brand);
        }

        .promo-switch-row {
            align-items: center;
            background: var(--surface-subtle);
            border: 1px solid var(--line);
            border-radius: .5rem;
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            padding: .85rem;
        }

        .promo-summary-card {
            position: sticky;
            top: 86px;
        }

        .promo-preview-code {
            background: var(--ink);
            border-radius: .5rem;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 900;
            overflow-wrap: anywhere;
            padding: .85rem;
        }

        .promo-preview-value {
            align-items: center;
            background: rgba(217, 119, 6, .12);
            border: 1px solid rgba(217, 119, 6, .24);
            border-radius: .5rem;
            color: #92400e;
            display: flex;
            font-size: 1.2rem;
            font-weight: 900;
            gap: .5rem;
            padding: .75rem .85rem;
        }

        .promo-summary-row {
            align-items: center;
            border-bottom: 1px solid var(--line);
            display: flex;
            gap: 1rem;
            justify-content: space-between;
            padding: .72rem 0;
        }

        .promo-summary-row:last-child {
            border-bottom: 0;
        }

        .promo-summary-label {
            color: var(--muted);
            font-size: .84rem;
            font-weight: 700;
        }

        .promo-summary-value {
            color: var(--ink);
            font-size: .9rem;
            font-weight: 800;
            text-align: right;
        }

        @media (max-width: 991.98px) {
            .promo-form-grid {
                grid-template-columns: 1fr;
            }

            .promo-summary-card {
                position: static;
            }
        }

        @media (max-width: 575.98px) {
            .promo-segment-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endonce

<div class="promo-form-grid" data-promo-form data-times-used="{{ $timesUsed }}">
    <form method="POST" action="{{ $action }}" class="app-card promo-form-card">
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <div class="promo-form-section">
            <div class="promo-section-title">
                <i class="bi bi-ticket-perforated text-primary"></i>
                Promo Identity
            </div>

            <div class="row g-3">
                <div class="col-lg-5">
                    <label for="code" class="form-label">Promo Code *</label>
                    <input type="text" id="code" name="code"
                        class="form-control form-control-lg promo-code-field @error('code') is-invalid @enderror"
                        value="{{ old('code', $promo?->code) }}" placeholder="SUMMER20" maxlength="50" required>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-7">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="2"
                        class="form-control @error('description') is-invalid @enderror"
                        placeholder="Weekend coffee promotion">{{ old('description', $promo?->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="promo-form-section">
            <div class="promo-section-title">
                <i class="bi bi-percent text-primary"></i>
                Discount
            </div>

            <div class="row g-3">
                <div class="col-lg-6">
                    <label class="form-label d-block">Discount Type *</label>
                    <div class="promo-segment-group">
                        <div>
                            <input type="radio" class="btn-check" name="discount_type" id="discount_type_percentage"
                                value="percentage" @checked($discountType === 'percentage') required>
                            <label class="promo-segment" for="discount_type_percentage">
                                <i class="bi bi-percent"></i>
                                <span class="fw-bold">Percentage</span>
                            </label>
                        </div>
                        <div>
                            <input type="radio" class="btn-check" name="discount_type" id="discount_type_fixed"
                                value="fixed" @checked($discountType === 'fixed') required>
                            <label class="promo-segment" for="discount_type_fixed">
                                <i class="bi bi-cash-coin"></i>
                                <span class="fw-bold">Fixed Amount</span>
                            </label>
                        </div>
                    </div>
                    @error('discount_type')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-lg-6">
                    <label for="discount_value" class="form-label">Discount Value *</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text" id="discount-value-addon">{{ $discountType === 'fixed' ? '$' : '%' }}</span>
                        <input type="number" id="discount_value" name="discount_value"
                            class="form-control @error('discount_value') is-invalid @enderror"
                            value="{{ old('discount_value', $promo?->discount_value) }}" placeholder="20" step="0.01"
                            min="0" required aria-describedby="discount-value-addon">
                        @error('discount_value')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="promo-form-section">
            <div class="promo-section-title">
                <i class="bi bi-sliders text-primary"></i>
                Rules
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="min_order_amount" class="form-label">Min Order Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="min_order_amount" name="min_order_amount"
                            class="form-control @error('min_order_amount') is-invalid @enderror"
                            value="{{ old('min_order_amount', $promo?->min_order_amount) }}" placeholder="0.00"
                            step="0.01" min="0">
                        @error('min_order_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="usage_limit" class="form-label">Usage Limit</label>
                    <input type="number" id="usage_limit" name="usage_limit"
                        class="form-control @error('usage_limit') is-invalid @enderror"
                        value="{{ old('usage_limit', $promo?->usage_limit) }}" placeholder="Unlimited" min="1">
                    @error('usage_limit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="valid_from" class="form-label">Valid From</label>
                    <input type="date" id="valid_from" name="valid_from"
                        class="form-control @error('valid_from') is-invalid @enderror"
                        value="{{ old('valid_from', $promo?->valid_from?->format('Y-m-d')) }}">
                    @error('valid_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="valid_until" class="form-label">Valid Until</label>
                    <input type="date" id="valid_until" name="valid_until"
                        class="form-control @error('valid_until') is-invalid @enderror"
                        value="{{ old('valid_until', $promo?->valid_until?->format('Y-m-d')) }}">
                    @error('valid_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="promo-form-section">
            <div class="promo-section-title">
                <i class="bi bi-toggle-on text-primary"></i>
                Status
            </div>

            <div class="promo-switch-row">
                <div>
                    <div class="fw-bold">Active Promo</div>
                    <div class="small text-muted">Available at checkout</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox" id="active" name="active" class="form-check-input" value="1"
                        @checked($isActive)>
                    <label class="form-check-label visually-hidden" for="active">Active</label>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> {{ $submitLabel }}
            </button>
            <a href="{{ route('promos.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>

    <aside class="app-card promo-summary-card" aria-label="Promo summary">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <h2 class="app-card-title mb-0">Summary</h2>
            <span id="promo-preview-status" class="badge {{ $isActive ? 'text-bg-success' : 'text-bg-secondary' }}">
                {{ $isActive ? 'Active' : 'Inactive' }}
            </span>
        </div>

        <div id="promo-preview-code" class="promo-preview-code mb-3">
            {{ old('code', $promo?->code) ?: 'PROMO' }}
        </div>

        <div id="promo-preview-discount" class="promo-preview-value mb-3">
            <i class="bi bi-percent"></i>
            <span>0%</span>
        </div>

        <div>
            <div class="promo-summary-row">
                <span class="promo-summary-label">Minimum</span>
                <span id="promo-preview-minimum" class="promo-summary-value">$0.00</span>
            </div>
            <div class="promo-summary-row">
                <span class="promo-summary-label">Usage</span>
                <span id="promo-preview-usage" class="promo-summary-value">{{ $timesUsed }} / Unlimited</span>
            </div>
            <div class="promo-summary-row">
                <span class="promo-summary-label">Starts</span>
                <span id="promo-preview-start" class="promo-summary-value">Not set</span>
            </div>
            <div class="promo-summary-row">
                <span class="promo-summary-label">Ends</span>
                <span id="promo-preview-end" class="promo-summary-value">Not set</span>
            </div>
        </div>
    </aside>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const root = document.querySelector('[data-promo-form]');
        if (!root) {
            return;
        }

        const codeInput = root.querySelector('#code');
        const discountValueInput = root.querySelector('#discount_value');
        const discountTypeInputs = root.querySelectorAll('input[name="discount_type"]');
        const discountValueAddon = root.querySelector('#discount-value-addon');
        const minOrderInput = root.querySelector('#min_order_amount');
        const usageLimitInput = root.querySelector('#usage_limit');
        const validFromInput = root.querySelector('#valid_from');
        const validUntilInput = root.querySelector('#valid_until');
        const activeInput = root.querySelector('#active');
        const timesUsed = Number(root.dataset.timesUsed || 0);

        const previewCode = root.querySelector('#promo-preview-code');
        const previewDiscount = root.querySelector('#promo-preview-discount span');
        const previewDiscountIcon = root.querySelector('#promo-preview-discount i');
        const previewMinimum = root.querySelector('#promo-preview-minimum');
        const previewUsage = root.querySelector('#promo-preview-usage');
        const previewStart = root.querySelector('#promo-preview-start');
        const previewEnd = root.querySelector('#promo-preview-end');
        const previewStatus = root.querySelector('#promo-preview-status');

        const selectedType = () => {
            const checked = root.querySelector('input[name="discount_type"]:checked');
            return checked ? checked.value : 'percentage';
        };

        const money = (value) => {
            const amount = Number(value);
            return Number.isFinite(amount) && amount > 0 ? '$' + amount.toFixed(2) : '$0.00';
        };

        const numberLabel = (value) => {
            const amount = Number(value);
            if (!Number.isFinite(amount) || amount <= 0) {
                return '0';
            }

            return Number.isInteger(amount) ? String(amount) : amount.toFixed(2);
        };

        const dateLabel = (value) => {
            if (!value) {
                return 'Not set';
            }

            const date = new Date(value + 'T00:00:00');
            if (Number.isNaN(date.getTime())) {
                return 'Not set';
            }

            return date.toLocaleDateString(undefined, {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        };

        const updatePreview = () => {
            const type = selectedType();
            const rawCode = codeInput.value.trim().toUpperCase();
            const usageLimit = usageLimitInput.value.trim();

            previewCode.textContent = rawCode || 'PROMO';
            discountValueAddon.textContent = type === 'fixed' ? '$' : '%';
            previewDiscountIcon.className = type === 'fixed' ? 'bi bi-cash-coin' : 'bi bi-percent';
            previewDiscount.textContent = type === 'fixed'
                ? money(discountValueInput.value)
                : numberLabel(discountValueInput.value) + '%';
            previewMinimum.textContent = money(minOrderInput.value);
            previewUsage.textContent = timesUsed + ' / ' + (usageLimit || 'Unlimited');
            previewStart.textContent = dateLabel(validFromInput.value);
            previewEnd.textContent = dateLabel(validUntilInput.value);
            previewStatus.textContent = activeInput.checked ? 'Active' : 'Inactive';
            previewStatus.className = activeInput.checked ? 'badge text-bg-success' : 'badge text-bg-secondary';
        };

        [
            codeInput,
            discountValueInput,
            minOrderInput,
            usageLimitInput,
            validFromInput,
            validUntilInput,
            activeInput,
            ...discountTypeInputs
        ].forEach((input) => {
            input.addEventListener('input', updatePreview);
            input.addEventListener('change', updatePreview);
        });

        updatePreview();
    });
</script>
