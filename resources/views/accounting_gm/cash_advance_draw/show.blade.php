@extends('layouts.app')
@section('title', 'Cash Advance Draw Review - GM')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('accounting-gm.cash-advance-draw.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Cash Advance Draw Review - GM</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">{{ $cashAdvanceDraw->number }}</h4>
                <p class="mb-0 text-muted">Document Number: <strong>{{ $cashAdvanceDraw->document_number }}</strong></p>
                <p class="mb-0 text-muted">Cost Center: <strong>{{ optional($cashAdvanceDraw->costCenter)->number ?? '' }} - {{ optional($cashAdvanceDraw->costCenter)->name ?? '' }}</strong></p>
            </div>
            <div>
                @php $statusText=optional($cashAdvanceDraw->status)->status??'Unknown'; @endphp
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
            <tr><th style="width:200px">Employee ID</th><td>{{ optional($cashAdvanceDraw->user ?? $cashAdvanceDraw)->employee_id ?? '-' }}</td></tr>
            <tr><th>Name</th><td>{{ optional($cashAdvanceDraw->user ?? $cashAdvanceDraw)->name ?? '-' }}</td></tr>
            <tr><th>Email</th><td>{{ optional($cashAdvanceDraw->user ?? $cashAdvanceDraw)->email ?? '-' }}</td></tr>
            <tr><th>Department</th><td>{{ optional(optional($cashAdvanceDraw->user ?? $cashAdvanceDraw)->department)->department ?? '-' }}</td></tr>
            <tr><th>Position</th><td>{{ optional(optional($cashAdvanceDraw->user ?? $cashAdvanceDraw)->position)->position ?? '-' }}</td></tr>
        </tbody></table></div>

        <h5>Uploaded Documents</h5>
        <div class="row mb-4"><div class="col-12"><ul class="list-group">
            @php $files=['car_form'=>'CAR Form','proposal_or_monitor_budget'=>'Proposal / Monitor Budget','budget_plan'=>'Budget Plan','other_document'=>'Other Document']; @endphp
            @foreach($files as $field=>$fileLabel)
            @if(!empty($cashAdvanceDraw->{$field}))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">{{ basename($cashAdvanceDraw->{$field}) }}</div></div><div><a href="{{ asset('storage/'.$cashAdvanceDraw->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div></li>
            @else
            @if(in_array($field, ['car_form','proposal_or_monitor_budget','budget_plan']))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">Not uploaded</div></div><div><span class="text-muted">—</span></div></li>
            @endif
            @endif
            @endforeach
        </ul></div></div>

        {{-- Hardfile Receipt Status --}}
        @php
        $staffApproved = $cashAdvanceDraw->approvals()->whereHas('role', function($q) { $q->where('sequence', 1); })->where('approval_status_id', 1)->exists();
        @endphp
        @if($cashAdvanceDraw->hardfile_received_at)
        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
        <div class="card-body">
            <div class="alert alert-success mb-0"><div class="d-flex align-items-center"><i class="fas fa-check-circle fa-2x mr-3"></i><div><strong>Hardfile Received</strong><br><span class="text-muted" style="color: white !important;">Received by: <strong>{{ optional($cashAdvanceDraw->hardfileReceivedByUser)->name ?? '-' }}</strong></span><br><span class="text-muted" style="color: white !important;">Date: <strong>{{ $cashAdvanceDraw->hardfile_received_at->format('d M Y H:i') }}</strong></span></div></div></div>
        </div></div></div>
        @elseif($staffApproved)
        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
        <div class="card-body">
            <div class="alert alert-secondary mb-0"><i class="fas fa-clock mr-2"></i> Waiting for hardfile submission to Accounting Staff.</div>
        </div></div></div>
        @endif

        {{-- Payment Receipt Status --}}
        @php
        $isFullyApproved = optional($cashAdvanceDraw->status)->slug === 'fully-approved';
        @endphp
        <div class="mt-4">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-money-bill-wave mr-2"></i>Payment Receipt</h4>
                </div>
                <div class="card-body">
                    @if($cashAdvanceDraw->is_paid)
                        <div class="alert alert-success mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle fa-2x mr-3"></i>
                                <div>
                                    <strong>Payment Processed</strong><br>
                                    <span class="text-muted" style="color: white !important;">Processed by: <strong>{{ optional($cashAdvanceDraw->paidByUser)->name ?? '-' }}</strong></span><br>
                                    <span class="text-muted" style="color: white !important;">Date: <strong>{{ optional($cashAdvanceDraw->paid_at)->format('d M Y H:i') }}</strong></span><br>
                                    <a href="{{ asset('storage/'.$cashAdvanceDraw->payment_receipt_path) }}" target="_blank" class="btn btn-sm btn-light mt-2 text-dark">View Receipt</a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-clock mr-2"></i> Payment has not been processed yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-header">@php $totalRevisions=$cashAdvanceDraw->revisions()->count();$maxRevisions=3; @endphp
                                        <h4><i class="fas fa-exclamation-circle mr-2"></i>Revisions ({{ $totalRevisions }}/{{ $maxRevisions }})</h4></div><div class="card-body">
                @if($cashAdvanceDraw->revisions()->count()===0)<p class="text-muted">No revisions requested yet.</p>
                @else<ul class="list-unstyled">
                    @foreach($cashAdvanceDraw->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
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
            <div class="col-md-6"><div class="card"><div class="card-header"><h4><i class="fas fa-check-circle mr-2"></i>Approval Chain</h4></div><div class="card-body"><div class="timeline">
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
                        @php $hasRejected=$cashAdvanceDraw->approvals()->where('approval_status_id',2)->exists(); @endphp
                        <div class="mt-4"><div class="card"><div class="card-header"><h4>GM Actions</h4></div>
                        <div class="card-body">
                            <button class="btn btn-success" data-toggle="modal" data-target="#approveModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-check"></i> Approve</button>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-times"></i> Reject</button>
                            <a href="{{ route('accounting-gm.cash-advance-draw.index') }}" class="btn btn-light">Back</a>
                        </div></div></div>
    </div></div></div></div></div>
</section>

<div class="modal fade" id="approveModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approve Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-gm.cash-advance-draw.approve', $cashAdvanceDraw) }}" method="POST">@csrf
        <div class="modal-body"><p>Are you sure you want to approve this Cash Advance Draw?</p><div class="form-group"><label>Remarks (Optional)</label><textarea class="form-control" name="remark" rows="3"></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Approve</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Reject Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-gm.cash-advance-draw.reject', $cashAdvanceDraw) }}" method="POST">@csrf
        <div class="modal-body"><p class="text-danger"><strong>Warning:</strong> Rejecting this document cannot be undone.</p><div class="form-group"><label>Reason for Rejection</label><textarea class="form-control" name="remark" rows="4" required></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-danger">Reject</button></div>
    </form>
</div></div></div>
@endsection
@push('scripts')
<style>.activity-timeline li{list-style:none;}</style>
@if(session()->has('success'))<script>iziToast.success({message:'{{ session()->get("success") }}',position:'topRight'});</script>@endif
@if(session()->has('error'))<script>iziToast.warning({message:'{{ session()->get("error") }}',position:'topRight'});</script>@endif
@endpush