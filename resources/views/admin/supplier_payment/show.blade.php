@extends('layouts.app')

@section('title', 'Supplier Payment Details - Admin')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('admin.supplier-payment.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Supplier Payment Details - Admin</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('admin.supplier-payment.index') }}">Supplier Payments</a></div>
            <div class="breadcrumb-item">Details</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">{{ $supplierPayment->number }}</h4>
                                <p class="mb-0 text-muted">Document Number: <strong>{{ $supplierPayment->document_number }}</strong></p>
                                <p class="mb-0 text-muted">Cost Center: <strong>{{ optional($supplierPayment->costCenter)->number }} - {{ optional($supplierPayment->costCenter)->name }}</strong></p>
                            </div>
                            <div>
                                @php
                                $statusText = optional($supplierPayment->status)->status ?? 'Unknown';
                                @endphp
                                @if(str_contains(strtolower($statusText), 'waiting'))
                                <span class="badge badge-warning">{{ $statusText }}</span>
                                @elseif(str_contains(strtolower($statusText), 'approved'))
                                <span class="badge badge-success">{{ $statusText }}</span>
                                @elseif(str_contains(strtolower($statusText), 'revision'))
                                <span class="badge badge-danger">{{ $statusText }}</span>
                                @else
                                <span class="badge badge-primary">{{ $statusText }}</span>
                                @endif
                            </div>
                        </div>

                        <hr />

                        <h5>Submitted By</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width:200px">Employee ID</th>
                                        <td>{{ optional($supplierPayment->user)->employee_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ optional($supplierPayment->user)->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ optional($supplierPayment->user)->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ optional(optional($supplierPayment->user)->department)->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td>{{ optional(optional($supplierPayment->user)->position)->position }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <h5>Uploaded Documents</h5>
                        <div class="row mb-4">
                            <div class="col-12">
                                <ul class="list-group">
                                    @php
                                    $files = [
                                    'spr_form' => 'SPR Form',
                                    'original_invoice' => 'Original Invoice',
                                    'copy_invoice' => 'Copy Invoice',
                                    'tax_invoice' => 'Tax Invoice',
                                    'agreement' => 'Agreement',
                                    'budget_plan' => 'Budget Plan',
                                    'internal_memo_entertain' => 'Internal Memo Entertain',
                                    'entertain_realization_form' => 'Entertain Realization Form',
                                    'minutes_of_meeting' => 'Minutes Of Meeting',
                                    'nominative_summary' => 'Nominative Summary',
                                    'calculation_summary' => 'Calculation Summary',
                                    ];
                                    @endphp

                                    @foreach($files as $field => $label)
                                    @if(!empty($supplierPayment->{$field}))
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $label }}</strong>
                                            <div class="text-muted small">{{ basename($supplierPayment->{$field}) }}</div>
                                        </div>
                                        <div>
                                            <a href="{{ asset('storage/' . $supplierPayment->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </li>
                                    @else
                                    @if(in_array($field, ['spr_form','original_invoice','copy_invoice','tax_invoice','agreement','budget_plan']))
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $label }}</strong>
                                            <div class="text-muted small">Not uploaded</div>
                                        </div>
                                        <div>
                                            <span class="text-muted">—</span>
                                        </div>
                                    </li>
                                    @endif
                                    @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Revisions Summary</h4>
                                    </div>
                                    <div class="card-body">
                                        @if($supplierPayment->revisions()->count() === 0)
                                        <p class="text-muted">No revisions in this document.</p>
                                        @else
                                        <ul class="list-unstyled activity-timeline">
                                            @foreach($supplierPayment->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
                                            <li class="media mb-3">
                                                <div class="media-body">
                                                    <div class="float-right text-muted small">{{ optional($rev->revision_at)->format('d M Y H:i') }}</div>
                                                    <h6 class="mt-0 mb-1">Revision #{{ $rev->revision_times }} — {{ optional($rev->user)->name }}</h6>
                                                    <div class="text-muted small">Status: {{ optional($rev->status)->status }}</div>
                                                    @if($rev->remark)
                                                    <div class="mt-1"><strong>Note:</strong> {{ $rev->remark }}</div>
                                                    @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Approval Chain</h4>
                                    </div>
                                    <div class="card-body">
                                        @php
                                        $steps = ['Accounting Staff','Accounting Manager','Accounting GM'];
                                        $approvals = $supplierPayment->approvals()->with(['user','status','role'])->get();
                                        @endphp

                                        <div class="timeline">
                                            @foreach($steps as $index => $step)
                                            @php
                                            $approval = $approvals->first(function($a) use ($step) {
                                            if(isset($a->role) && is_object($a->role) && isset($a->role->name)){
                                            return strtolower($a->role->name) === strtolower($step);
                                            }
                                            if(isset($a->approval_role) && is_string($a->approval_role)){
                                            return strtolower($a->approval_role) === strtolower($step);
                                            }
                                            return false;
                                            }) ?? $approvals->get($index) ?? null;
                                            @endphp

                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between">
                                                    <div><strong>{{ $step }}</strong></div>
                                                    <div>
                                                        @if($approval)
                                                        @php $aStatus = optional($approval->status)->status ?? null; @endphp
                                                        @if($aStatus && str_contains(strtolower($aStatus), 'approved'))
                                                        <span class="badge badge-success">{{ $aStatus }}</span>
                                                        @elseif($aStatus && str_contains(strtolower($aStatus), 'rejected'))
                                                        <span class="badge badge-danger">{{ $aStatus }}</span>
                                                        @elseif($aStatus)
                                                        <span class="badge badge-warning">{{ $aStatus }}</span>
                                                        @else
                                                        <span class="badge badge-secondary">Pending</span>
                                                        @endif
                                                        @else
                                                        <span class="badge badge-secondary">Pending</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-muted small">
                                                    @if($approval)
                                                    {{ optional($approval->user)->name }}
                                                    @if(isset($approval->approved_at)) — {{ optional($approval->approved_at)->format('d M Y H:i') }} @endif
                                                    @if(!empty($approval->remark))<div class="mt-1">Remark: {{ $approval->remark }}</div>@endif
                                                    @else
                                                    <div class="mt-1">No action yet.</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Admin Actions</h4>
                                </div>
                                <div class="card-body">
                                    <button class="btn btn-info" data-toggle="modal" data-target="#updateStatusModal">
                                        <i class="fas fa-cog"></i> Force Update Status
                                    </button>

                                    <a href="{{ route('admin.supplier-payment.index') }}" class="btn btn-light">Back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {{-- Hardfile Receipt Status --}}
        @php
        $staffApproved = $supplierPayment->approvals()->whereHas('role', function($q) { $q->where('sequence', 1); })->where('approval_status_id', 1)->exists();
        @endphp
        @if($supplierPayment->hardfile_received_at)
        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
        <div class="card-body">
            <div class="alert alert-success mb-0"><div class="d-flex align-items-center"><i class="fas fa-check-circle fa-2x mr-3"></i><div><strong>Hardfile Received</strong><br><span class="text-muted">Received by: <strong>{{ optional($supplierPayment->hardfileReceivedByUser)->name ?? '-' }}</strong></span><br><span class="text-muted">Date: <strong>{{ $supplierPayment->hardfile_received_at->format('d M Y H:i') }}</strong></span></div></div></div>
        </div></div></div>
        @elseif($staffApproved)
        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
        <div class="card-body">
            <div class="alert alert-warning mb-0"><i class="fas fa-clock mr-2"></i> Waiting for hardfile submission to Accounting Staff.</div>
        </div></div></div>
        @endif
</section>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Force Update Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.supplier-payment.update-status', $supplierPayment) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-warning"><strong>⚠️ Warning:</strong> This will bypass the normal approval process.</p>
                    <div class="form-group">
                        <label for="document_status_id">New Status</label>
                        <select class="form-control @error('document_status_id') is-invalid @enderror" id="document_status_id" name="document_status_id" required>
                            <option value="">-- Select Status --</option>
                            @foreach(\App\Models\DocumentStatus::all() as $status)
                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                            @endforeach
                        </select>
                        @error('document_status_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="remark">Reason/Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .activity-timeline li {
        list-style: none;
    }
</style>

@if(session()->has('success'))
<script>
    iziToast.success({
        message: '{{ session()->get("success") }}',
        position: 'topRight'
    });
</script>
@endif

@if($errors->any())
@foreach($errors->all() as $error)
<script>
    iziToast.error({
        message: '{{ $error }}',
        position: 'topRight'
    });
</script>
@endforeach
@endif

@if(session()->has('error'))
<script>
    iziToast.warning({
        message: '{{ session()->get("error") }}',
        position: 'topRight'
    });
</script>
@endif

@endpush