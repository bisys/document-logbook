@extends('layouts.app')
@section('title', 'Cash Advance Realization - Manager Review')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Cash Advance Realization - Manager Review</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Cash Advance Realization</div>
        </div>
    </div>
    <div class="section-body">
        <div class="row"><div class="col-12"><div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Cash Advance Realization List</h4>
                
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='all'?'active':'' }}" href="?status=all">All <span class="badge badge-primary">{{ $counts['all'] }}</span></a></li>
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='waiting-approval-staff'?'active':'' }}" href="?status=waiting-approval-staff">Waiting Staff <span class="badge badge-warning">{{ $counts['waiting-approval-staff'] }}</span></a></li>
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='waiting-approval-manager'?'active':'' }}" href="?status=waiting-approval-manager">Waiting Manager <span class="badge badge-warning">{{ $counts['waiting-approval-manager'] }}</span></a></li>
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='waiting-approval-gm'?'active':'' }}" href="?status=waiting-approval-gm">Waiting GM <span class="badge badge-warning">{{ $counts['waiting-approval-gm'] }}</span></a></li>
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='waiting-revision'?'active':'' }}" href="?status=waiting-revision">Waiting Revision <span class="badge badge-warning">{{ $counts['waiting-revision'] }}</span></a></li>
                    <li class="nav-item"><a class="nav-link {{ $statusFilter==='fully-approved'?'active':'' }}" href="?status=fully-approved">Fully Approved <span class="badge badge-success">{{ $counts['fully-approved'] }}</span></a></li>
                </ul>
                <div class="table-responsive"><table class="table table-striped">
                    <thead><tr><th>#</th><th>Submitted By</th><th>Cost Center</th><th>Revisions</th><th>Status</th><th>Submitted At</th><th>Action</th></tr></thead>
                    <tbody>
                        @forelse($cashAdvanceRealizations as $i => $cashAdvanceRealization)
                        <tr>
                            <td>{{ $i+1 }}</td>
                            
                            <td>{{ optional(optional($cashAdvanceRealization->draw)->user)->name ?? '-' }}</td>
                            <td>{{ optional(optional(optional($cashAdvanceRealization->draw))->costCenter)->number ?? '-' }}</td>
                            <td>{{ $cashAdvanceRealization->revisions->count() }}</td>
                            <td>
                                @php $st=optional($cashAdvanceRealization->status)->status??'Unknown'; @endphp
                                @if(str_contains(strtolower($st),'waiting'))<span class="badge badge-warning">{{ $st }}</span>
                                @elseif(str_contains(strtolower($st),'approved'))<span class="badge badge-success">{{ $st }}</span>
                                @elseif(str_contains(strtolower($st),'rejected'))<span class="badge badge-danger">{{ $st }}</span>
                                @else<span class="badge badge-primary">{{ $st }}</span>@endif
                            </td>
                            <td>{{ $cashAdvanceRealization->created_at->format('d M Y H:i') }}</td>
                            <td><a href="{{ route('accounting-manager.cash-advance-realization.show', $cashAdvanceRealization) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No documents found.</td></tr>
                        @endforelse
                    </tbody>
                </table></div>
            </div>
        </div></div></div>
    </div>
</section>
@endsection
@push('scripts')
@if(session()->has('success'))<script>iziToast.success({message:'{{ session()->get("success") }}',position:'topRight'});</script>@endif
@if(session()->has('error'))<script>iziToast.warning({message:'{{ session()->get("error") }}',position:'topRight'});</script>@endif
@endpush