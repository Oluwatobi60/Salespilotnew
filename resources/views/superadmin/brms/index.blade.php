@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'BRM Management')

@section('superadmin_page_styles')
@media (max-width: 767.98px) {
    .col-hide-sm { display: none !important; }
}
@endSection

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Business Relation Managers</h5>
        <p class="text-muted small mb-0">Manage and assign BRMs to customer accounts</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" action="{{ route('superadmin.brms') }}" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ $search }}"
                   class="form-control form-control-sm" style="min-width:160px;max-width:240px;flex:1 1 160px;"
                   placeholder="Search name, email, region…">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            @if($search)
                <a href="{{ route('superadmin.brms') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
        <a href="{{ route('superadmin.brms.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i><span class="d-none d-sm-inline">Register BRM</span><span class="d-sm-none">New</span>
        </a>
    </div>
</div>

<div class="sa-card p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size:0.875rem;">
            <thead style="background:#f8f7ff;">
                <tr>
                    <th class="px-4 py-3 fw-semibold text-secondary col-hide-sm">#</th>
                    <th class="px-3 py-3 fw-semibold text-secondary">Name</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Email</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Phone</th>
                    <th class="px-3 py-3 fw-semibold text-secondary col-hide-sm">Region</th>
                    <th class="px-3 py-3 fw-semibold text-secondary text-center col-hide-sm">Customers</th>
                    <th class="px-3 py-3 fw-semibold text-secondary">Status</th>
                    <th class="px-3 py-3 fw-semibold text-secondary text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($brms as $brm)
                    <tr>
                        <td class="px-4 text-muted col-hide-sm">{{ $loop->iteration + ($brms->currentPage() - 1) * $brms->perPage() }}</td>

                        <td class="px-3">
                            <div class="fw-semibold">{{ $brm->name }}</div>
                            @if($brm->address)
                                <div class="text-muted" style="font-size:0.75rem;">{{ $brm->address }}</div>
                            @endif
                        </td>

                        <td class="px-3 col-hide-sm">{{ $brm->email }}</td>
                        <td class="px-3 col-hide-sm">{{ $brm->phone ?? '—' }}</td>
                        <td class="px-3 col-hide-sm">{{ $brm->region ?? '—' }}</td>

                        <td class="px-3 text-center col-hide-sm">
                            <span class="badge rounded-pill" style="background:#ede9fe;color:#6f42c1;font-size:0.8rem;">
                                {{ $brm->customers_count }}
                            </span>
                        </td>

                        <td class="px-3">
                            @if($brm->status)
                                <span class="badge rounded-pill text-bg-success">Active</span>
                            @else
                                <span class="badge rounded-pill text-bg-danger">Inactive</span>
                            @endif
                        </td>

                        <td class="px-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="{{ route('superadmin.brms.edit', $brm->id) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST"
                                      action="{{ route('superadmin.brms.toggle', $brm->id) }}"
                                      onsubmit="return confirm('{{ $brm->status ? 'Deactivate' : 'Activate' }} this BRM?')">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $brm->status ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                            title="{{ $brm->status ? 'Deactivate' : 'Activate' }}">
                                        <i class="bi bi-{{ $brm->status ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-person-badge fs-2 d-block mb-2"></i>
                            No BRMs found{{ $search ? ' for "' . $search . '"' : '' }}.
                            <a href="{{ route('superadmin.brms.create') }}" class="d-block mt-2 small">Register the first BRM</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($brms->hasPages())
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $brms->firstItem() }}–{{ $brms->lastItem() }} of {{ $brms->total() }} BRMs
            </div>
            {{ $brms->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
