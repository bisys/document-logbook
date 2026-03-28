@extends('layouts.app')

@section('title', 'International Trip')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>International Trip - Approval Queue</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">International Trip</a></div>
            <div class="breadcrumb-item">Approval Queue</div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <ul class="nav nav-pills">
                            @php
                            $tabs = [
                            'all' => 'All',
                            'waiting-approval-staff' => 'Waiting Approval Staff',
                            'waiting-approval-manager' => 'Waiting Approval Manager',
                            'waiting-approval-gm' => 'Waiting Approval GM',
                            'waiting-revision' => 'Waiting Revision',
                            'fully-approved' => 'Fully Approved',
                            ];
                            @endphp
                            @foreach($tabs as $key => $label)
                            <li class="nav-item">
                                <a class="nav-link {{ $statusFilter === $key ? 'active' : '' }}" href="?status={{ $key }}">
                                    {{ $label }}
                                    <span @if($key === 'waiting-revision' || $key === 'waiting-approval-staff' || $key === 'waiting-approval-manager' || $key === 'waiting-approval-gm') class="badge badge-warning">{{ $counts[$key] ?? 0 }}</span>
                                    @elseif($key === 'fully-approved') <span class="badge badge-success">{{ $counts[$key] ?? 0 }}</span>
                                    @else <span class="badge badge-primary">{{ $counts[$key] ?? 0 }}</span>
                                    @endif
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Document Queue</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>Document Number</th>
                                        <th>Submitted By</th>
                                        <th>Cost Center</th>
                                        <th>Revisions</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($internationalTrips as $internationalTrip)
                                    <tr>
                                        <td>
                                            <strong>{{ $internationalTrip->number }}</strong><br>
                                            <small class="text-muted">{{ $internationalTrip->document_number }}</small>
                                        </td>
                                        <td>
                                            {{ optional($internationalTrip->user)->name }}<br>
                                            <small class="text-muted">{{ optional(optional($internationalTrip->user)->department)->department }}</small>
                                        </td>
                                        <td>{{ optional($internationalTrip->costCenter)->number }} - {{ optional($internationalTrip->costCenter)->name }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $internationalTrip->revisions()->count() }} revisions</span>
                                        </td>
                                        <td>
                                            @php
                                            $statusText = optional($internationalTrip->status)->status ?? 'Unknown';
                                            @endphp
                                            @if(str_contains(strtolower($statusText), 'waiting approval staff'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'waiting approval manager'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'waiting approval gm'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'waiting revision'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'fully approved'))
                                            <span class="badge badge-success">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'rejected'))
                                            <span class="badge badge-danger">{{ $statusText }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($internationalTrip->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('accounting-staff.international-trip.show', $internationalTrip) }}" class="btn btn-sm btn-primary">Review</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="/assets/js/page/modules-datatables.js"></script>

@if(session()->has('success'))
<script>
    iziToast.success({
        message: '{{ session()->get("success") }}',
        position: 'topRight'
    });
</script>
@endif
@endpush