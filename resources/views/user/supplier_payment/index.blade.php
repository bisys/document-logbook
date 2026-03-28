@extends('layouts.app')

@section('title', 'Supplier Payment')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Supplier Payment</h1>
        <div class="section-header-button">
            <a href="{{ route('user.supplier-payment.create') }}" class="btn btn-primary">Create New</a>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Supplier Payment</a></div>
            <div class="breadcrumb-item">
                @if($statusFilter === 'all') All Supplier Payment @elseif($statusFilter === 'waiting-approval-staff') Waiting Approval Staff @elseif($statusFilter === 'waiting-approval-manager') Waiting Approval Manager @elseif($statusFilter === 'waiting-approval-gm') Waiting Approval GM @elseif($statusFilter === 'waiting-revision') Waiting Revision @elseif($statusFilter === 'fully-approved') Fully Approved @endif
            </div>
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
                        <h4>
                            @if($statusFilter === 'all') All Supplier Payment
                            @elseif($statusFilter === 'waiting-approval-staff') Waiting Approval Staff
                            @elseif($statusFilter === 'waiting-approval-manager') Waiting Approval Manager
                            @elseif($statusFilter === 'waiting-approval-gm') Waiting Approval GM
                            @elseif($statusFilter === 'waiting-revision') Waiting Revision
                            @elseif($statusFilter === 'fully-approved') Fully Approved
                            @endif
                        </h4>
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
                                    @foreach($supplierPayments as $payment)
                                    <tr>
                                        <td>
                                            <strong>{{ $payment->number }}</strong><br>
                                            <small class="text-muted">{{ $payment->document_number }}</small>
                                        </td>
                                        <td>
                                            {{ optional($payment->user)->name }}<br>
                                            <small class="text-muted">{{ optional(optional($payment->user)->department)->department }}</small>
                                        </td>
                                        <td>{{ optional($payment->costCenter)->number }} - {{ optional($payment->costCenter)->name }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $payment->revisions()->count() }} revisions</span>
                                        </td>
                                        <td>
                                            @php
                                            $statusText = optional($payment->status)->status ?? 'Unknown';
                                            @endphp
                                            @if(str_contains(strtolower($statusText), 'waiting approval'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'waiting revision'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'approved'))
                                            <span class="badge badge-success">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'rejected'))
                                            <span class="badge badge-danger">{{ $statusText }}</span>
                                            @endif
                                        </td>
                                        <td>{{ optional($payment->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('user.supplier-payment.show', $payment) }}" class="btn btn-sm btn-primary">Detail</a>
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