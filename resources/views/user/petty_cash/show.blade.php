@extends('layouts.app')

@section('title', 'Petty Cash Detail')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.petty-cash.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Petty Cash Detail</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('user.petty-cash.index') }}">Petty Cash</a></div>
            <div class="breadcrumb-item">Detail</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 class="mb-1">{{ $pettyCash->number }}</h4>
                                <p class="mb-0 text-muted">Document Number: <strong>{{ $pettyCash->document_number }}</strong></p>
                                <p class="mb-0 text-muted">Cost Center: <strong>{{ optional($pettyCash->costCenter)->number }} - {{ optional($pettyCash->costCenter)->name }}</strong></p>
                            </div>
                            <div>
                                @php
                                $statusText = optional($pettyCash->status)->status ?? 'Unknown';
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
                            </div>
                        </div>

                        <hr />

                        <h5>Submitted By</h5>
                        <div class="table-responsive mb-3">
                            <table class="table table-sm table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width:200px">Employee ID</th>
                                        <td>{{ optional($pettyCash->user)->employee_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ optional($pettyCash->user)->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ optional($pettyCash->user)->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ optional(optional($pettyCash->user)->department)->department }}</td>
                                    </tr>
                                    <tr>
                                        <th>Position</th>
                                        <td>{{ optional(optional($pettyCash->user)->position)->position }}</td>
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
                                    'pcr_form' => 'PCR Form',
                                    'original_invoice' => 'Original Invoice',
                                    'copy_invoice' => 'Copy Invoice',
                                    'budget_plan' => 'Budget Plan',
                                    'internal_memo_entertain' => 'Internal Memo Entertain',
                                    'entertain_realization_form' => 'Entertain Realization Form',
                                    'minutes_of_meeting' => 'Minutes Of Meeting',
                                    'nominative_summary' => 'Nominative Summary',
                                    'cic_form' => 'CIC Form',
                                    ];
                                    @endphp

                                    @foreach($files as $field => $label)
                                    @if(!empty($pettyCash->{$field}))
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $label }}</strong>
                                            <div class="text-muted small">{{ basename($pettyCash->{$field}) }}</div>
                                        </div>
                                        <div>
                                            <a href="{{ asset('storage/' . $pettyCash->{$field}) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                        </div>
                                    </li>
                                    @else
                                    @if(in_array($field, ['pcr_form','original_invoice','copy_invoice','budget_plan']))
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

                        {{-- Hardfile Receipt Status --}}
                        @php
                        $staffApproved = $pettyCash->approvals()->whereHas('role', function($q) { $q->where('sequence', 1); })->where('approval_status_id', 1)->exists();
                        @endphp
                        @if($pettyCash->hardfile_received_at)
                        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
                        <div class="card-body">
                            <div class="alert alert-success mb-0"><div class="d-flex align-items-center"><i class="fas fa-check-circle fa-2x mr-3"></i><div><strong>Hardfile Received</strong><br><span class="text-muted" style="color: white !important;">Received by: <strong>{{ optional($pettyCash->hardfileReceivedByUser)->name ?? '-' }}</strong></span><br><span class="text-muted" style="color: white !important;">Date: <strong>{{ $pettyCash->hardfile_received_at->format('d M Y H:i') }}</strong></span></div></div></div>
                        </div></div></div>
                        @elseif($staffApproved)
                        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-0"><i class="fas fa-clock mr-2"></i> Waiting for hardfile submission to Accounting Staff.</div>
                        </div></div></div>
                        @else
                        <div class="mt-4"><div class="card"><div class="card-header"><h4><i class="fas fa-box mr-2"></i>Hardfile Receipt</h4></div>
                        <div class="card-body">
                            <div class="alert alert-secondary mb-0">
                                <i class="fas fa-info-circle mr-2"></i> Hardfile receipt can only be recorded after the document is approved by Accounting Staff.
                            </div>
                        </div></div></div>
                        @endif

                        {{-- Payment Receipt Status --}}
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
                                    @else
                                        <div class="alert alert-secondary mb-0">
                                            <i class="fas fa-clock mr-2"></i> Payment has not been processed yet.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        @php
                                        $totalRevisions = $pettyCash->revisions()->count();
                                        $maxRevisions = 3;
                                        @endphp
                                        <h4>Revisions ({{ $totalRevisions }}/{{ $maxRevisions }})
                                            @if($pendingRevisions->isNotEmpty())
                                            <span class="badge badge-danger ml-2">{{ $pendingRevisions->count() }} Pending</span>
                                            @endif
                                        </h4>
                                    </div>
                                    <div class="card-body">
                                        @if($pendingRevisions->isNotEmpty())
                                        <div class="alert alert-warning mb-3">
                                            <strong>⚠️ Action Required:</strong> You have {{ $pendingRevisions->count() }} revision request(s) to address.
                                        </div>
                                        @endif

                                        <ul class="list-unstyled activity-timeline">
                                            @forelse($pettyCash->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
                                            <li class="media mb-3">
                                                <div class="media-body">
                                                    <div class="float-right text-muted small">{{ optional($rev->revision_at)->format('d M Y H:i') }}</div>
                                                    <h6 class="mt-0 mb-1">Revision #{{ $rev->revision_times }} — {{ optional($rev->user)->name }}</h6>
                                                    <div class="text-muted small">
                                                        Status:
                                                        @php $rStatus = optional($rev->status)->status ?? 'Unknown'; @endphp
                                                        @if(str_contains(strtolower($rStatus), 'revision requested'))
                                                        <span class="badge badge-warning">{{ $rStatus }}</span>
                                                        @elseif(str_contains(strtolower($rStatus), 'revised'))
                                                        <span class="badge badge-success">{{ $rStatus }}</span>
                                                        @else
                                                        <span class="badge badge-primary">{{ $rStatus }}</span>
                                                        @endif
                                                    </div>
                                                    @if($rev->remark)
                                                    <div class="mt-2 p-2 bg-light rounded">
                                                        <strong>Revision Note:</strong><br>
                                                        {{ $rev->remark }}
                                                    </div>
                                                    @endif

                                                    @if($rev->revision_status_id == 1)
                                                    <div class="mt-2">
                                                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#revisionModal{{ $rev->id }}">
                                                            <i class="fas fa-edit"></i> Revise Documents
                                                        </button>
                                                    </div>
                                                    @endif
                                                </div>
                                            </li>
                                            @empty
                                            <li class="text-muted">No revisions yet.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Approval Chain</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="timeline">
                                            @forelse($approvalChain as $item)
                                            <div class="mb-4">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $item['role']->name }}</strong>
                                                    </div>
                                                    <div>
                                                        @if($item['status'] === 'approved')
                                                        <span class="badge badge-success">Approved</span>
                                                        @elseif($item['status'] === 'rejected')
                                                        <span class="badge badge-danger">Rejected</span>
                                                        @else
                                                        <span class="badge badge-secondary">Pending</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-muted small mt-2">
                                                    @if($item['approval'])
                                                    <div><strong>{{ optional($item['approval']->user)->name }}</strong></div>
                                                    @if(!empty($item['approval']->remark))
                                                    <div><strong>Remark:</strong> {{ $item['approval']->remark }}</div>
                                                    @endif
                                                    @if(isset($item['approval']->approval_at))
                                                    <div>{{ \Carbon\Carbon::parse($item['approval']->approval_at)->format('d M Y H:i') }}</div>
                                                    @endif
                                                    @else
                                                    <div class="text-muted">Waiting for approval...</div>
                                                    @endif
                                                </div>
                                                @if(!$loop->last)
                                                <div class="mt-2" style="border-left: 2px solid #dee2e6; margin-left: 10px; height: 20px;"></div>
                                                @endif
                                            </div>
                                            @empty
                                            <p class="text-muted">No approval chain available.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('user.petty-cash.index') }}" class="btn btn-light">Back</a>
                            @if($canEdit)
                            <a href="{{ route('user.petty-cash.edit', $pettyCash->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit Document
                            </a>
                            @else
                            <button class="btn btn-primary" disabled title="Cannot edit while document is in revision status or has revisions">
                                <i class="fas fa-edit"></i> Edit Document (In Revision / Has Revisions)
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Revision Modals -->
@foreach($pettyCash->revisions()->where('revision_status_id', 1)->get() as $revision)
<div class="modal fade" id="revisionModal{{ $revision->id }}" tabindex="-1" role="dialog" aria-labelledby="revisionModalLabel{{ $revision->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revisionModalLabel{{ $revision->id }}">
                    Revise Documents - Revision #{{ $revision->revision_times }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('user.petty-cash.submit-revision', [$pettyCash, $revision]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Revision Request:</strong><br>
                        {{ $revision->remark }}
                    </div>

                    <div class="form-group">
                        <label for="document_number">Document Number</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $pettyCash->document_number }}">
                    </div>

                    <h6 class="mt-4 mb-3"><strong>Upload Revised Documents</strong></h6>
                    <p class="text-muted small">Only upload files that have been revised. Leave blank to keep the current file.</p>

                    @php
                    $files = [
                    'pcr_form' => 'PCR Form',
                    'original_invoice' => 'Original Invoice',
                    'copy_invoice' => 'Copy Invoice',
                    'budget_plan' => 'Budget Plan',
                    'internal_memo_entertain' => 'Internal Memo Entertain',
                    'entertain_realization_form' => 'Entertain Realization Form',
                    'minutes_of_meeting' => 'Minutes Of Meeting',
                    'nominative_summary' => 'Nominative Summary',
                    'cic_form' => 'CIC Form',
                    ];
                    @endphp

                    <div class="row">
                        @foreach(array_chunk($files, 2, true) as $chunk)
                        @foreach($chunk as $field => $label)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $field }}">{{ $label }}</label>
                                <small class="d-block text-muted mb-2">
                                    @if(!empty($pettyCash->{$field}))
                                    Current: {{ basename($pettyCash->{$field}) }}
                                    @else
                                    Not yet uploaded
                                    @endif
                                </small>
                                <input type="file" class="form-control-file" id="{{ $field }}" name="{{ $field }}" accept=".pdf,.doc,.docx,.xls,.xlsx">
                            </div>
                        </div>
                        @endforeach
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Submit Revision</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

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