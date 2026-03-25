@extends('layouts.app')

@section('title', 'Supplier Payment Detail')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.supplier-payment.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Supplier Payment Detail</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ route('user.supplier-payment.index') }}">Supplier Payment</a></div>
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
                                        @php
                                        $totalRevisions = $supplierPayment->revisions()->count();
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
                                            @forelse($supplierPayment->revisions()->with(['user','status'])->orderByDesc('revision_at')->get() as $rev)
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
                            <a href="{{ route('user.supplier-payment.index') }}" class="btn btn-light">Back</a>
                            @if($canEdit)
                            <a href="{{ route('user.supplier-payment.edit', $supplierPayment->id) }}" class="btn btn-primary">
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
@foreach($supplierPayment->revisions()->where('revision_status_id', 1)->get() as $revision)
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
            <form action="{{ route('user.supplier-payment.submit-revision', [$supplierPayment, $revision]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Revision Request:</strong><br>
                        {{ $revision->remark }}
                    </div>

                    <div class="form-group">
                        <label for="document_number">Document Number</label>
                        <input type="text" class="form-control" id="document_number" name="document_number" value="{{ $supplierPayment->document_number }}">
                    </div>

                    <h6 class="mt-4 mb-3"><strong>Upload Revised Documents</strong></h6>
                    <p class="text-muted small">Only upload files that have been revised. Leave blank to keep the current file.</p>

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

                    <div class="row">
                        @foreach(array_chunk($files, 2, true) as $chunk)
                        @foreach($chunk as $field => $label)
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="{{ $field }}">{{ $label }}</label>
                                <small class="d-block text-muted mb-2">
                                    @if(!empty($supplierPayment->{$field}))
                                    Current: {{ basename($supplierPayment->{$field}) }}
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