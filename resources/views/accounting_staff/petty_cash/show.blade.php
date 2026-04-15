@extends('layouts.app')
@section('title', 'Petty Cash Review - Staff')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('accounting-staff.petty-cash.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Petty Cash Review - Staff</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">{{ $pettyCash->number }}</h4>
                <p class="mb-0 text-muted">Document Number: <strong>{{ $pettyCash->document_number }}</strong></p>
                <p class="mb-0 text-muted">Cost Center: <strong>{{ optional($pettyCash->costCenter)->number ?? '' }} - {{ optional($pettyCash->costCenter)->name ?? '' }}</strong></p>
            </div>
            <div>
                @php $statusText=optional($pettyCash->status)->status??'Unknown'; @endphp
                @if(str_contains(strtolower($statusText),'waiting approval staff'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'waiting approval manager'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'waiting approval gm'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'waiting revision'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'fully approved'))<span class="badge badge-success">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'rejected'))<span class="badge badge-danger">{{ $statusText }}</span>@endif
            </div>
        </div>
        <hr/>
        <h5>Submitted By</h5>
        <div class="table-responsive mb-3"><table class="table table-sm table-bordered"><tbody>
            <tr><th style="width:200px">Employee ID</th><td>{{ optional($pettyCash->user ?? $pettyCash)->employee_id ?? '-' }}</td></tr>
            <tr><th>Name</th><td>{{ optional($pettyCash->user ?? $pettyCash)->name ?? '-' }}</td></tr>
            <tr><th>Email</th><td>{{ optional($pettyCash->user ?? $pettyCash)->email ?? '-' }}</td></tr>
            <tr><th>Department</th><td>{{ optional(optional($pettyCash->user ?? $pettyCash)->department)->department ?? '-' }}</td></tr>
            <tr><th>Position</th><td>{{ optional(optional($pettyCash->user ?? $pettyCash)->position)->position ?? '-' }}</td></tr>
        </tbody></table></div>

        <h5>Uploaded Documents</h5>
        <div class="row mb-4"><div class="col-12"><ul class="list-group">
            @php $files=['pcr_form'=>'PCR Form','original_invoice'=>'Original Invoice','copy_invoice'=>'Copy Invoice','internal_memo_entertain'=>'Internal Memo Entertain','entertain_realization_form'=>'Entertain Realization Form','minutes_of_meeting'=>'Minutes Of Meeting','nominative_summary'=>'Nominative Summary','cic_form'=>'CIC Form','budget_plan'=>'Budget Plan',]; @endphp
            @foreach($files as $field=>$fileLabel)
            @if(!empty($pettyCash->{$field}))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">{{ basename($pettyCash->{$field}) }}</div></div><div><a href="{{ asset('storage/'.$pettyCash->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div></li>
            @else
            @if(in_array($field, ['pcr_form','original_invoice','copy_invoice','budget_plan']))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">Not uploaded</div></div><div><span class="text-muted">—</span></div></li>
            @endif
            @endif
            @endforeach
        </ul></div></div>

        {{-- Hardfile Receipt Section --}}
        @php
        $staffApproved = $pettyCash->approvals()->whereHas('role', function($q) { $q->where('sequence', 1); })->where('approval_status_id', 1)->exists();
        @endphp
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4>
                </div>
                <div class="card-body">
                    @if($pettyCash->hardfile_received_at)
                        <div class="alert alert-success mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <strong>Hardfile Received</strong><br>
                                    <span class="text-muted" style="color: white !important;">Received by: <strong>{{ optional($pettyCash->hardfileReceivedByUser)->name ?? '-' }}</strong></span><br>
                                    <span class="text-muted" style="color: white !important;">Date: <strong>{{ $pettyCash->hardfile_received_at->format('d M Y H:i') }}</strong></span>
                                </div>
                            </div>
                        </div>
                    @elseif($staffApproved)
                        <p class="text-muted mb-3">Document has been approved. You can now record the hardfile receipt.</p>
                        <button class="btn btn-info" data-toggle="modal" data-target="#receiveHardfileModal">
                            <i class="fas fa-box"></i> Receive Hardfile
                        </button>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle mr-2"></i> Hardfile receipt can only be recorded after the document is approved by Accounting Staff.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Payment Receipt Section --}}
        @php
        $isFullyApproved = optional($pettyCash->status)->slug === 'fully-approved';
        @endphp
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-money-bill-wave mr-2"></i>Payment Receipt</h4>
                </div>
                <div class="card-body">
                    @if($pettyCash->is_paid)
                        <div class="alert alert-success mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <strong>Payment Processed</strong><br>
                                    <span class="text-muted" style="color: white !important;">Processed by: <strong>{{ optional($pettyCash->paidByUser)->name ?? '-' }}</strong></span><br>
                                    <span class="text-muted" style="color: white !important;">Date: <strong>{{ optional($pettyCash->paid_at)->format('d M Y H:i') }}</strong></span><br>
                                    <a href="{{ asset('storage/'.$pettyCash->payment_receipt_path) }}" target="_blank" class="btn btn-sm btn-light mt-2 text-dark">View Receipt</a>
                                </div>
                            </div>
                        </div>
                    @elseif($pettyCash->hardfile_received_at && $isFullyApproved)
                        <p class="text-muted mb-3">Document is fully approved and hardfile has been received. You can now process the payment.</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#processPaymentModal">
                            <i class="fas fa-money-bill-wave"></i> Process Payment
                        </button>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-info-circle mr-2"></i> Payment can only be processed after the document is fully approved and hardfile is received.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-header"><h4>Revisions ({{ $totalRevisions }}/{{ $maxRevisions }})</h4></div><div class="card-body">
                @if($pettyCash->revisions()->count()===0)<p class="text-muted">No revisions requested yet.</p>
                @else<ul class="list-unstyled">
                    @foreach($pettyCash->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
                    <li class="media mb-3"><div class="media-body">
                        <div class="float-right text-muted small">{{ optional($rev->revision_at)->format('d M Y H:i') }}</div>
                        <h6 class="mt-0 mb-1">Revision #{{ $rev->revision_times }} — {{ optional($rev->user)->name }}</h6>
                        <div class="text-muted small">Status: @php $rs=optional($rev->status)->status??'Unknown'; @endphp
                            @if(str_contains(strtolower($rs),'requested'))<span class="badge badge-warning">{{ $rs }}</span>
                            @elseif(str_contains(strtolower($rs),'revised'))<span class="badge badge-success">{{ $rs }}</span>
                            @else<span class="badge badge-primary">{{ $rs }}</span>@endif
                        </div>
                        @if($rev->remark)<div class="mt-2 p-2 bg-light rounded"><strong>Revision Note:</strong><br>{{ $rev->remark }}</div>@endif
                    </div></li>
                    @endforeach</ul>@endif
            </div></div></div>
            <div class="col-md-6"><div class="card"><div class="card-header"><h4>Approval Chain</h4></div><div class="card-body"><div class="timeline">
                @forelse($approvalChain as $item)
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-start"><div><strong>{{ $item['role']->name }}</strong></div><div>
                        @if($item['status']==='approved')<span class="badge badge-success">Approved</span>
                        @elseif($item['status']==='rejected')<span class="badge badge-danger">Rejected</span>
                        @else<span class="badge badge-secondary">Pending</span>@endif
                    </div></div>
                    <div class="text-muted small mt-2">
                        @if($item['approval'])<div><strong>{{ optional($item['approval']->user)->name }}</strong></div>
                        @if(!empty($item['approval']->remark))<div><strong>Remark:</strong> {{ $item['approval']->remark }}</div>@endif
                        @if(isset($item['approval']->approval_at))<div>{{ \Carbon\Carbon::parse($item['approval']->approval_at)->format('d M Y H:i') }}</div>@endif
                        @else<div class="text-muted">Waiting for approval...</div>@endif
                    </div>
                    @if(!$loop->last)<div class="mt-2" style="border-left:2px solid #dee2e6;margin-left:10px;height:20px;"></div>@endif
                </div>
                @empty<p class="text-muted">No approval chain available.</p>@endforelse
            </div></div></div></div>
        </div>
                        @php $hasRejected=$pettyCash->approvals()->where('approval_status_id',2)->exists(); @endphp
                        <div class="mt-4"><div class="card"><div class="card-header"><h4>Actions</h4></div>
                        <div class="card-body">
                            @if($totalRevisions<$maxRevisions)
                            <button class="btn btn-warning" data-toggle="modal" data-target="#addRevisionModal" {{ $hasRejected?'disabled title="Document already rejected"':'' }}><i class="fas fa-redo"></i> Request Revision</button>
                            @else
                            <button class="btn btn-warning" disabled><i class="fas fa-redo"></i> Request Revision (Max)</button>
                            @endif
                            @if($canApprove)
                            <button class="btn btn-success" data-toggle="modal" data-target="#approveModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-check"></i> Approve</button>
                            @else
                            <button class="btn btn-success" disabled><i class="fas fa-check"></i> Approve (Pending Revisions)</button>
                            @endif
                            <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-times"></i> Reject</button>
                            <a href="{{ route('accounting-staff.petty-cash.index') }}" class="btn btn-light">Back</a>
                        </div></div></div>
    </div></div></div></div></div>
</section>
<div class="modal fade" id="addRevisionModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Request Revision</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-staff.petty-cash.add-revision', $pettyCash) }}" method="POST">@csrf
        <div class="modal-body"><div class="form-group"><label>Revision Note</label><textarea class="form-control" name="remark" rows="4" required></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-warning">Request Revision</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="approveModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approve Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-staff.petty-cash.approve', $pettyCash) }}" method="POST">@csrf
        <div class="modal-body"><p>Are you sure you want to approve this Petty Cash?</p><div class="form-group"><label>Remarks (Optional)</label><textarea class="form-control" name="remark" rows="3"></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Approve</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Reject Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-staff.petty-cash.reject', $pettyCash) }}" method="POST">@csrf
        <div class="modal-body"><p class="text-danger"><strong>Warning:</strong> Rejecting this document cannot be undone.</p><div class="form-group"><label>Reason for Rejection</label><textarea class="form-control" name="remark" rows="4" required></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="receiveHardfileModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Confirm Hardfile Receipt</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-staff.petty-cash.receive-hardfile', $pettyCash) }}" method="POST">@csrf
        <div class="modal-body"><div class="text-center mb-3"><i class="fas fa-box fa-3x text-info mb-3"></i><p>Are you sure you have received the hardfile for this document?</p><p class="text-muted small">This will record the current date and time as the hardfile receipt date.</p></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-info">Confirm Receipt</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="processPaymentModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Process Payment</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-staff.petty-cash.process-payment', $pettyCash) }}" method="POST" enctype="multipart/form-data">@csrf
        <div class="modal-body">
            <div class="text-center mb-3"><i class="fas fa-money-bill-wave fa-3x text-primary mb-3"></i><p>Upload the payment transfer receipt to mark this document as paid.</p></div>
            <div class="form-group">
                <label>Payment Receipt (Max 500KB, JPG/PNG/PDF)</label>
                <input type="file" name="payment_receipt" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Process Payment</button></div>
    </form>
</div></div></div>
@endsection
@push('scripts')
<style>.activity-timeline li{list-style:none;}</style>
@if(session()->has('success'))<script>iziToast.success({message:'{{ session()->get("success") }}',position:'topRight'});</script>@endif
@if(session()->has('error'))<script>iziToast.warning({message:'{{ session()->get("error") }}',position:'topRight'});</script>@endif
@endpush