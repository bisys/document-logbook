@extends('layouts.app')
@section('title', 'Petty Cash Review - Manager')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('accounting-manager.petty-cash.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Petty Cash Review - Manager</h1>
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
                @if(str_contains(strtolower($statusText),'waiting'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'approved'))<span class="badge badge-success">{{ $statusText }}</span>
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

        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-header">@php $totalRevisions=$pettyCash->revisions()->count();$maxRevisions=3; @endphp
                                        <h4>Revisions ({{ $totalRevisions }}/{{ $maxRevisions }})</h4></div><div class="card-body">
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
                        <div class="mt-4"><div class="card"><div class="card-header"><h4>Manager Actions</h4></div>
                        <div class="card-body">
                            <button class="btn btn-success" data-toggle="modal" data-target="#approveModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-check"></i> Approve</button>
                            <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal" {{ $hasRejected?'disabled':'' }}><i class="fas fa-times"></i> Reject</button>
                            <a href="{{ route('accounting-manager.petty-cash.index') }}" class="btn btn-light">Back</a>
                        </div></div></div>
    </div></div></div></div></div>
</section>

<div class="modal fade" id="approveModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Approve Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-manager.petty-cash.approve', $pettyCash) }}" method="POST">@csrf
        <div class="modal-body"><p>Are you sure you want to approve this Petty Cash?</p><div class="form-group"><label>Remarks (Optional)</label><textarea class="form-control" name="remark" rows="3"></textarea></div></div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button><button type="submit" class="btn btn-success">Approve</button></div>
    </form>
</div></div></div>
<div class="modal fade" id="rejectModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Reject Document</h5><button type="button" class="close" data-dismiss="modal"><span>&times;</span></button></div>
    <form action="{{ route('accounting-manager.petty-cash.reject', $pettyCash) }}" method="POST">@csrf
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