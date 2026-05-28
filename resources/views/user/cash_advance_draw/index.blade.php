@extends('layouts.app')

@section('title', 'Cash Advance Draw')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Cash Advance Draw</h1>
        <div class="section-header-button">
            <a href="{{ route('user.cash-advance-draw.create') }}" class="btn btn-primary">Create New</a>
        </div>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Cash Advance Draw</a></div>
            <div class="breadcrumb-item">
                @if($statusFilter === 'all') All Cash Advance Draw @elseif($statusFilter === 'waiting-approval-staff') Waiting Approval Staff @elseif($statusFilter === 'waiting-approval-manager') Waiting Approval Manager @elseif($statusFilter === 'waiting-approval-gm') Waiting Approval GM @elseif($statusFilter === 'waiting-revision') Waiting Revision @elseif($statusFilter === 'fully-approved') Fully Approved @endif
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
                            @if($statusFilter === 'all') All Cash Advance Draw
                            @elseif($statusFilter === 'waiting-approval-staff') Waiting Approval Staff
                            @elseif($statusFilter === 'waiting-approval-manager') Waiting Approval Manager
                            @elseif($statusFilter === 'waiting-approval-gm') Waiting Approval GM
                            @elseif($statusFilter === 'waiting-revision') Waiting Revision
                            @elseif($statusFilter === 'fully-approved') Fully Approved
                            @endif
                        </h4>
                        <div class="card-header-action">
                            <form id="bulk-submit-form" action="{{ route('user.cash-advance-draw.bulk-submit-hardfile') }}" method="POST">
                                @csrf
                                <div id="bulk-inputs"></div>
                                <button type="button" class="btn btn-primary" id="btn-bulk-submit" style="display: none;">Submit Hardfile Selected (<span id="selected-count">0</span>)</button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" data-checkboxes="mygroup" data-checkbox-role="dad" class="custom-control-input" id="checkbox-all">
                                                <label for="checkbox-all" class="custom-control-label">&nbsp;</label>
                                            </div>
                                        </th>
                                        <th>No</th>
                                        <th>Document Number</th>
                                        <th>Submitted By</th>
                                        <th>Cost Center</th>
                                        <th>Revisions</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th>Hardfile Received At</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashAdvanceDraws as $cashAdvanceDraw)
                                    <tr>
                                        <td class="text-center">
                                            @php
                                                $staffRole = \App\Models\ApprovalRole::where('sequence', 1)->first();
                                                $hasStaffApproved = $staffRole ? $cashAdvanceDraw->approvals->where('approval_role_id', $staffRole->id)->where('approval_status_id', 1)->isNotEmpty() : false;
                                            @endphp
                                            @if(!$cashAdvanceDraw->is_hardfile_submitted && $hasStaffApproved)
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input doc-checkbox" id="checkbox-{{ $cashAdvanceDraw->id }}" value="{{ $cashAdvanceDraw->id }}">
                                                <label for="checkbox-{{ $cashAdvanceDraw->id }}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $loop->iteration }}
                                        </td>
                                        <td>
                                            <strong>{{ $cashAdvanceDraw->number }}</strong><br>
                                            <small class="text-muted">{{ $cashAdvanceDraw->document_number }}</small>
                                        </td>
                                        <td>
                                            {{ optional($cashAdvanceDraw->user)->name }}<br>
                                            <small class="text-muted">{{ optional(optional($cashAdvanceDraw->user)->department)->department }}</small>
                                        </td>
                                        <td>{{ optional($cashAdvanceDraw->costCenter)->number }} - {{ optional($cashAdvanceDraw->costCenter)->name }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $cashAdvanceDraw->revisions()->count() }} revisions</span>
                                        </td>
                                        <td>
                                            @php
                                            $statusText = optional($cashAdvanceDraw->status)->status ?? 'Unknown';
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
                                        <td>{{ optional($cashAdvanceDraw->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            @if($cashAdvanceDraw->hardfile_received_at)
                                            {{ $cashAdvanceDraw->hardfile_received_at->format('d M Y H:i') }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            @if($cashAdvanceDraw->is_paid)
                                            <span class="badge badge-success">Paid</span>
                                            @else
                                            <span class="badge badge-warning">Not Paid</span>
                                            @endif
                                            <br>
                                            <small class="text-muted">
                                            @if($cashAdvanceDraw->is_paid)
                                            {{ $cashAdvanceDraw->paid_at->format('d M Y H:i') }}
                                            @else
                                            -
                                            @endif
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.cash-advance-draw.show', $cashAdvanceDraw) }}" class="btn btn-sm btn-primary">Detail</a>
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

@if(session()->has('error'))
<script>
    iziToast.warning({
        message: '{{ session()->get("error") }}',
        position: 'topRight'
    });
</script>
@endif

<!-- Script for Bulk Submit -->
<script>
    $(document).ready(function() {
        function updateBulkSubmitButton() {
            var selectedCount = $('.doc-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            if (selectedCount > 0) {
                $('#btn-bulk-submit').show();
            } else {
                $('#btn-bulk-submit').hide();
            }
        }

        $('#table-1').on('change', 'input[type="checkbox"]', function() {
            setTimeout(updateBulkSubmitButton, 50);
        });

        $('#btn-bulk-submit').click(function() {
            var selectedIds = [];
            $('.doc-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            swal({
                title: 'Submit Hardfile?',
                text: 'You are about to submit hardfile for ' + selectedIds.length + ' document(s).',
                icon: 'warning',
                buttons: true,
            })
            .then((willSubmit) => {
                if (willSubmit) {
                    $('#bulk-inputs').empty();
                    selectedIds.forEach(function(id) {
                        $('#bulk-inputs').append('<input type="hidden" name="document_ids[]" value="' + id + '">');
                    });
                    
                    $('#bulk-submit-form').submit();
                }
            });
        });
    });
</script>
@endpush