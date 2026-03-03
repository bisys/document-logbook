@extends('layouts.app')

@section('title', 'Supplier Payments Management')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Supplier Payments Management</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Supplier Payments</div>
            <div class="breadcrumb-item">Management</div>
        </div>
    </div>
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card mb-0">
                    <div class="card-body">
                        <form method="GET" class="form-inline" style="gap: 10px;">
                            <div class="form-group">
                                <label for="status_id" class="mr-2">Status:</label>
                                <select class="form-control" id="status_id" name="status_id" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->status }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>All Supplier Payments</h4>
                        <div class="card-header-action">
                            <span class="badge badge-primary">{{ $supplierPayments->count() }} Total</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>Document Number</th>
                                        <th>Submitted By</th>
                                        <th>Cost Center</th>
                                        <th>Status</th>
                                        <th>Revisions</th>
                                        <th>Approvals</th>
                                        <th>Submitted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($supplierPayments as $payment)
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
                                            @php
                                            $statusText = optional($payment->status)->status ?? 'Unknown';
                                            @endphp
                                            @if(str_contains(strtolower($statusText), 'waiting'))
                                            <span class="badge badge-warning">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'approved'))
                                            <span class="badge badge-success">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'revision'))
                                            <span class="badge badge-danger">{{ $statusText }}</span>
                                            @elseif(str_contains(strtolower($statusText), 'rejected'))
                                            <span class="badge badge-dark">{{ $statusText }}</span>
                                            @else
                                            <span class="badge badge-primary">{{ $statusText }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $payment->revisions()->count() }}</span>
                                        </td>
                                        <td>
                                            @php
                                            $approvalCount = $payment->approvals()->count();
                                            $approvedCount = $payment->approvals()->where('approval_status_id', 3)->count();
                                            @endphp
                                            <small>{{ $approvedCount }}/3</small>
                                        </td>
                                        <td>
                                            {{ optional($payment->created_at)->format('d M Y H:i') }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.supplier-payment.show', $payment) }}" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">No supplier payments found</td>
                                    </tr>
                                    @endforelse
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
<script>
    $(function() {
        $('#table-1').dataTable();
    });
</script>
@endpush