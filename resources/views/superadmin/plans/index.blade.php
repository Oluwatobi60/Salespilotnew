@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Subscription Plans')

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Subscription Plans</h5>
        <p class="text-muted small mb-0">Manage pricing plans available to users</p>
    </div>
    <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> New Plan
    </a>
</div>

<div class="row g-4">
    @forelse($plans as $plan)
        <div class="col-12 col-md-6 col-xl-4">
            <div class="sa-card h-100 d-flex flex-column" style="border-top: 4px solid {{ $plan->is_active ? '#7c3aed' : '#d1d5db' }};">
                <!-- Header -->
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-capitalize">{{ $plan->name }}</h6>
                        @if($plan->is_popular)
                            <span class="badge" style="background:#ede9fe;color:#6f42c1;font-size:0.7rem;">
                                <i class="bi bi-star-fill me-1"></i> Popular
                            </span>
                        @endif
                    </div>
                    <span class="badge rounded-pill {{ $plan->is_active ? 'text-bg-success' : 'text-bg-secondary' }}" style="font-size:0.7rem;">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                <!-- Price -->
                <div class="mb-3">
                    <span class="fw-bold" style="font-size:1.6rem;">&#8358;{{ number_format($plan->monthly_price, 2) }}</span>
                    <span class="text-muted small"> / month</span>
                </div>

                <!-- Description -->
                @if($plan->description)
                    <p class="text-muted small mb-3">{{ $plan->description }}</p>
                @endif

                <!-- Limits -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge" style="background:#f0f2f5;color:#374151;font-size:0.72rem;">
                        <i class="bi bi-person-check me-1"></i>{{ $plan->max_managers }} Manager{{ $plan->max_managers > 1 ? 's' : '' }}
                    </span>
                    <span class="badge" style="background:#f0f2f5;color:#374151;font-size:0.72rem;">
                        <i class="bi bi-people me-1"></i>{{ $plan->max_staff ? $plan->max_staff . ' Staff' : 'Unlimited Staff' }}
                    </span>
                    <span class="badge" style="background:#f0f2f5;color:#374151;font-size:0.72rem;">
                        <i class="bi bi-diagram-3 me-1"></i>{{ $plan->max_branches ? $plan->max_branches . ' Branch' . ($plan->max_branches > 1 ? 'es' : '') : 'Unlimited Branches' }}
                    </span>
                    @if($plan->trial_days > 0)
                        <span class="badge" style="background:#dcfce7;color:#16a34a;font-size:0.72rem;">
                            <i class="bi bi-gift me-1"></i>{{ $plan->trial_days }}-day trial
                        </span>
                    @endif
                </div>

                <!-- Features -->
                @if(is_array($plan->features) && count($plan->features) > 0)
                    <ul class="list-unstyled mb-3" style="font-size:0.8rem;">
                        @foreach($plan->features as $feature)
                            <li class="py-1" style="border-bottom:1px solid #f3f4f6;">
                                <i class="bi bi-check-circle-fill me-2" style="color:#16a34a;font-size:0.75rem;"></i>{{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <!-- Stats + Actions -->
                <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                    <span class="text-muted small">
                        <i class="bi bi-people me-1"></i>
                        {{ $plan->user_subscriptions_count }} subscription{{ $plan->user_subscriptions_count !== 1 ? 's' : '' }}
                    </span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('superadmin.plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('superadmin.plans.toggle', $plan->id) }}">
                            @csrf
                            <button type="submit"
                                    class="btn btn-sm {{ $plan->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                    title="{{ $plan->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="bi bi-{{ $plan->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="sa-card text-center py-5">
                <i class="bi bi-card-list text-muted" style="font-size:2.5rem;"></i>
                <p class="text-muted mt-3 mb-3">No subscription plans found.</p>
                <a href="{{ route('superadmin.plans.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Create First Plan
                </a>
            </div>
        </div>
    @endforelse
</div>

@endsection
