@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'User Details')

@section('superadmin_layout_content')

<div class="mb-4">
    <a href="{{ route('superadmin.customers') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
</div>

@php
    $isActive = (int)($user->status ?? 1) === 1;
    $fullName = trim(($user->first_name ?? '') . ' ' . ($user->surname ?? '')) ?: $user->email;
    $sub      = $user->currentSubscription;
    $plan     = $sub?->subscriptionPlan;
    $daysLeft = ($sub && $sub->end_date) ? (int) now()->startOfDay()->diffInDays($sub->end_date, false) : null;
@endphp

<div class="row g-4">

    {{-- Profile Card --}}
    <div class="col-lg-4">
        <div class="sa-card h-100">
            <div class="text-center mb-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                     style="width:72px;height:72px;background:#ede9fe;font-size:1.8rem;color:#6f42c1;font-weight:700;">
                    {{ strtoupper(substr($user->first_name ?? $user->email, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1">{{ $fullName }}</h5>
                <div class="text-muted small">{{ $user->email }}</div>
                <div class="mt-2">
                    @if($isActive)
                        <span class="badge text-bg-success rounded-pill">Active</span>
                    @else
                        <span class="badge text-bg-danger rounded-pill">Inactive</span>
                    @endif
                </div>
            </div>

            <hr>

            <dl class="row mb-0" style="font-size:0.875rem;">
                <dt class="col-5 text-muted fw-normal">Business</dt>
                <dd class="col-7 fw-semibold">{{ $user->business_name ?? '—' }}</dd>

                <dt class="col-5 text-muted fw-normal">Phone</dt>
                <dd class="col-7">{{ $user->phone_number ?? '—' }}</dd>

                <dt class="col-5 text-muted fw-normal">State</dt>
                <dd class="col-7">{{ $user->state ?? '—' }}</dd>

                <dt class="col-5 text-muted fw-normal">LGA</dt>
                <dd class="col-7">{{ $user->local_govt ?? '—' }}</dd>

                <dt class="col-5 text-muted fw-normal">Address</dt>
                <dd class="col-7">{{ $user->address ?? '—' }}</dd>

                <dt class="col-5 text-muted fw-normal">Joined</dt>
                <dd class="col-7">{{ $user->created_at->format('M d, Y') }}</dd>
            </dl>

            <hr>

            {{-- Account toggle --}}
            <form method="POST" action="{{ route('superadmin.customers.toggle', $user->id) }}"
                  onsubmit="return confirm('{{ $isActive ? 'Deactivate' : 'Activate' }} this account?')">
                @csrf
                <button type="submit"
                        class="btn btn-sm w-100 {{ $isActive ? 'btn-outline-danger' : 'btn-outline-success' }}">
                    <i class="bi bi-{{ $isActive ? 'toggle-on' : 'toggle-off' }} me-1"></i>
                    {{ $isActive ? 'Deactivate Account' : 'Activate Account' }}
                </button>
            </form>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="d-flex flex-column gap-4">

            {{-- Current Subscription --}}
            <div class="sa-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold mb-0">Current Subscription</h6>
                    <form method="POST" action="{{ route('superadmin.customers.reminder', $user->id) }}"
                          onsubmit="return confirm('Send subscription reminder to {{ $user->email }}?')">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-envelope me-1"></i> Send Reminder
                        </button>
                    </form>
                </div>

                @if($sub && $plan)
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Plan</div>
                            <div class="fw-semibold">{{ $plan->name }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Amount Paid</div>
                            <div class="fw-semibold">&#8358;{{ number_format($sub->amount_paid, 2) }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Expires</div>
                            <div class="fw-semibold">{{ $sub->end_date->format('M d, Y') }}</div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="text-muted small">Days Left</div>
                            @if($daysLeft > 0)
                                <span class="badge text-bg-success">{{ $daysLeft }}d left</span>
                            @elseif($daysLeft === 0)
                                <span class="badge text-bg-warning">Expires today</span>
                            @else
                                <span class="badge text-bg-danger">Expired</span>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">No active subscription.</p>
                @endif
            </div>

            {{-- Assign BRM --}}
            <div class="sa-card">
                <h6 class="fw-bold mb-3">Assign BRM</h6>
                <form method="POST" action="{{ route('superadmin.customers.assign-brm', $user->id) }}">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col">
                            <label class="form-label text-muted small mb-1">Business Relation Manager</label>
                            <select name="brm_id" class="form-select">
                                <option value="">— No BRM assigned —</option>
                                @foreach($activeBrms as $b)
                                    <option value="{{ $b->id }}" {{ $user->brm_id == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}{{ $b->region ? ' — ' . $b->region : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-check me-1"></i> Save Assignment
                            </button>
                        </div>
                    </div>
                    @if($user->brm)
                        <div class="mt-2 small text-muted">
                            Currently assigned to <strong>{{ $user->brm->name }}</strong>
                            @if($user->brm->region) ({{ $user->brm->region }}) @endif
                        </div>
                    @endif
                </form>
            </div>

            {{-- Subscription History --}}
            <div class="sa-card p-0 overflow-hidden">
                <div class="px-4 py-3 border-bottom">
                    <h6 class="fw-bold mb-0">Subscription History</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0" style="font-size:0.85rem;">
                        <thead style="background:#f8f7ff;">
                            <tr>
                                <th class="px-4 py-2 fw-semibold text-secondary">Plan</th>
                                <th class="px-3 py-2 fw-semibold text-secondary">Amount</th>
                                <th class="px-3 py-2 fw-semibold text-secondary">Start</th>
                                <th class="px-3 py-2 fw-semibold text-secondary">End</th>
                                <th class="px-3 py-2 fw-semibold text-secondary">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subscriptions as $s)
                                <tr>
                                    <td class="px-4">{{ $s->subscriptionPlan?->name ?? '—' }}</td>
                                    <td class="px-3">&#8358;{{ number_format($s->amount_paid, 2) }}</td>
                                    <td class="px-3">{{ $s->start_date?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-3">{{ $s->end_date?->format('M d, Y') ?? '—' }}</td>
                                    <td class="px-3">
                                        @if($s->status === 'active')
                                            <span class="badge text-bg-success">Active</span>
                                        @elseif($s->status === 'expired')
                                            <span class="badge text-bg-danger">Expired</span>
                                        @else
                                            <span class="badge text-bg-secondary">{{ ucfirst($s->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-3 text-muted">No subscription history.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</div>

@endsection
