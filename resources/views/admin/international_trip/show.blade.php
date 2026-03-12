@extends('layouts.app')
@section('title', 'International Trip Review - Admin')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('admin.international-trip.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>International Trip Review - Admin</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h4 class="mb-1">{{ $internationalTrip->number }}</h4>
                <p class="mb-0 text-muted">Document Number: <strong>{{ $internationalTrip->document_number }}</strong></p>
                <p class="mb-0 text-muted">Cost Center: <strong>{{ optional($internationalTrip->costCenter)->number ?? '' }} - {{ optional($internationalTrip->costCenter)->name ?? '' }}</strong></p>
            </div>
            <div>
                @php $statusText=optional($internationalTrip->status)->status??'Unknown'; @endphp
                @if(str_contains(strtolower($statusText),'waiting'))<span class="badge badge-warning">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'approved'))<span class="badge badge-success">{{ $statusText }}</span>
                @elseif(str_contains(strtolower($statusText),'rejected'))<span class="badge badge-danger">{{ $statusText }}</span>@endif
            </div>
        </div>
        <hr/>
        <h5>Submitted By</h5>
        <div class="table-responsive mb-3"><table class="table table-sm table-bordered"><tbody>
            <tr><th style="width:200px">Employee ID</th><td>{{ optional($internationalTrip->user ?? $internationalTrip)->employee_id ?? '-' }}</td></tr>
            <tr><th>Name</th><td>{{ optional($internationalTrip->user ?? $internationalTrip)->name ?? '-' }}</td></tr>
            <tr><th>Email</th><td>{{ optional($internationalTrip->user ?? $internationalTrip)->email ?? '-' }}</td></tr>
            <tr><th>Department</th><td>{{ optional(optional($internationalTrip->user ?? $internationalTrip)->department)->department ?? '-' }}</td></tr>
            <tr><th>Position</th><td>{{ optional(optional($internationalTrip->user ?? $internationalTrip)->position)->position ?? '-' }}</td></tr>
        </tbody></table></div>

        <h5>Uploaded Documents</h5>
        <div class="row mb-4"><div class="col-12"><ul class="list-group">
            @php $files=['itar_form'=>'ITAR Form','internal_memo'=>'Internal Memo','summary_bussiness_trip'=>'Summary Business Trip','overseas_allowance_form'=>'Overseas Allowance Form','bussiness_trip_allowance'=>'Business Trip Allowance','rate'=>'Rate','budget_plan'=>'Budget Plan',]; @endphp
            @foreach($files as $field=>$fileLabel)
            @if(!empty($internationalTrip->{$field}))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">{{ basename($internationalTrip->{$field}) }}</div></div><div><a href="{{ asset('storage/'.$internationalTrip->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a></div></li>
            @else
            @if(in_array($field, ['itar_form','internal_memo','summary_bussiness_trip','overseas_allowance_form','bussiness_trip_allowance','rate','budget_plan']))
            <li class="list-group-item d-flex justify-content-between align-items-center"><div><strong>{{ $fileLabel }}</strong><div class="text-muted small">Not uploaded</div></div><div><span class="text-muted">—</span></div></li>
            @endif
            @endif
            @endforeach
        </ul></div></div>

        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-header">@php $totalRevisions=$internationalTrip->revisions()->count();$maxRevisions=3; @endphp
                                        <h4>Revisions ({{ $totalRevisions }}/{{ $maxRevisions }})</h4></div><div class="card-body">
                @if($internationalTrip->revisions()->count()===0)<p class="text-muted">No revisions requested yet.</p>
                @else<ul class="list-unstyled">
                    @foreach($internationalTrip->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
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
                        <div class="mt-4"><div class="card"><div class="card-header"><h4>Admin Actions</h4></div>
                        <div class="card-body">
                            <form action="{{ route('admin.international-trip.update-status', $internationalTrip) }}" method="POST" class="form-inline">
                                @csrf
                                <select name="document_status_id" class="form-control mr-2">
                                    @foreach(\App\Models\DocumentStatus::all() as $s)
                                    <option value="{{ $s->id }}" {{ $internationalTrip->document_status_id==$s->id?'selected':'' }}>{{ $s->status }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="remark" class="form-control mr-2" placeholder="Remark (optional)">
                                <button type="submit" class="btn btn-primary">Update Status</button>
                            </form>
                            <a href="{{ route('admin.international-trip.index') }}" class="btn btn-light mt-2">Back</a>
                        </div></div></div>
    </div></div></div></div></div>
</section>

@endsection
@push('scripts')
<style>.activity-timeline li{list-style:none;}</style>
@if(session()->has('success'))<script>iziToast.success({message:'{{ session()->get("success") }}',position:'topRight'});</script>@endif
@if(session()->has('error'))<script>iziToast.warning({message:'{{ session()->get("error") }}',position:'topRight'});</script>@endif
@endpush