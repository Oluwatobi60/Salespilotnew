@extends('superadmin.layouts.layout')
@section('superadmin_page_title', 'Superadmin Management')

@section('superadmin_page_styles')
<style>
@media (max-width: 767.98px) {
    .col-hide-sm { display: none !important; }
}
</style>
@endSection

@section('superadmin_layout_content')

<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h5 class="fw-bold mb-1">Administrators</h5>
        <p class="text-muted small mb-0">Manage system superadministrators</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <form method="GET" action="{{ route('superadmin.admins') }}" class="d-flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ $search }}"
                   class="form-control form-control-sm" style="min-width:160px;max-width:240px;flex:1 1 160px;"
                   placeholder="Search name, email…">
            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
            @if($search)
                <a href="{{ route('superadmin.admins') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            @endif
        </form>
        <a href="{{ route('superadmin.admins.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i><span class="d-none d-sm-inline">New Admin</span><span class="d-sm-none">New</span>
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
                    <th class="px-3 py-3 fw-semibold text-secondary text-center">Status</th>
                    <th class="px-3 py-3 fw-semibold text-secondary text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                    <tr>
                        <td class="px-4 text-muted col-hide-sm">{{ $loop->iteration + ($admins->currentPage() - 1) * $admins->perPage() }}</td>

                        <td class="px-3">
                            <div class="fw-semibold">{{ $admin->name }}</div>
                            @if(Auth::guard('superadmin')->id() === $admin->id)
                                <div class="text-muted" style="font-size:0.75rem;">(You)</div>
                            @endif
                        </td>

                        <td class="px-3 col-hide-sm">{{ $admin->email }}</td>
                        <td class="px-3 col-hide-sm">{{ $admin->phone ?? '—' }}</td>

                        <td class="px-3 text-center">
                            @if($admin->status)
                                <span class="badge rounded-pill text-bg-success">Active</span>
                            @else
                                <span class="badge rounded-pill text-bg-danger">Disabled</span>
                            @endif
                        </td>

                        <td class="px-3 text-center">
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <a href="{{ route('superadmin.admins.edit', $admin->id) }}"
                                   class="btn btn-sm btn-outline-secondary" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(Auth::guard('superadmin')->id() !== $admin->id)
                                <form method="POST"
                                      action="{{ route('superadmin.admins.toggle', $admin->id) }}"
                                      onsubmit="return confirm('Are you sure you want to {{ $admin->status ? 'disable' : 'activate' }} this admin?')">
                                    @csrf
                                    <button type="submit"
                                            class="btn btn-sm {{ $admin->status ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                            title="{{ $admin->status ? 'Disable Admin' : 'Activate Admin' }}">
                                        <i class="bi bi-{{ $admin->status ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-lock fs-2 d-block mb-2"></i>
                            No Admins found{{ $search ? ' for "' . $search . '"' : '' }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($admins->hasPages())
        <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="text-muted small">
                Showing {{ $admins->firstItem() }}–{{ $admins->lastItem() }} of {{ $admins->total() }} Admins
            </div>
            {{ $admins->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection
