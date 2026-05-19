@extends('layouts.app')

@section('title', 'International Trip Realization')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>International Trip Realization - Approval Queue</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">International Trip Realization</a></div>
            <div class="breadcrumb-item">Approval Queue</div>
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
                        <h4>Document Queue</h4>
                        <div class="card-header-action">
                            <form id="bulk-receive-form" action="{{ route('accounting-staff.international-trip-realization.bulk-receive-hardfile') }}" method="POST">
                                @csrf
                                <div id="bulk-inputs"></div>
                                <button type="button" class="btn btn-primary" id="btn-bulk-receive" style="display: none;">Receive Hardfile Selected (<span id="selected-count">0</span>)</button>
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
                                        <th>Document Number</th>
                                        <th>Linked International Trip</th>
                                        <th>Submitted By</th>
                                        <th>Cost Center</th>
                                        <th>Revisions</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th>Hardfile Received At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($internationalTripRealizations as $internationalTripRealization)
                                    <tr>
                                        <td class="text-center">
                                            @php
                                                $staffRole = \App\Models\ApprovalRole::where('sequence', 1)->first();
                                                $hasStaffApproved = $staffRole ? $internationalTripRealization->approvals->where('approval_role_id', $staffRole->id)->where('approval_status_id', 1)->isNotEmpty() : false;
                                            @endphp
                                            @if(!$internationalTripRealization->hardfile_received_at && $hasStaffApproved)
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" data-checkboxes="mygroup" class="custom-control-input doc-checkbox" id="checkbox-{{ $internationalTripRealization->id }}" value="{{ $internationalTripRealization->id }}">
                                                <label for="checkbox-{{ $internationalTripRealization->id }}" class="custom-control-label">&nbsp;</label>
                                            </div>
                                            @endif
                                        </td>
                                        <td><strong>{{ $internationalTripRealization->number }}</strong></td>
                                        <td>
                                            <strong>{{ $internationalTripRealization->trip->number }}</strong><br>
                                            <small class="text-muted">{{ $internationalTripRealization->trip->document_number }}</small>
                                        </td>
                                        <td>
                                            {{ optional($internationalTripRealization->user)->name }}<br>
                                            <small class="text-muted">{{ optional(optional($internationalTripRealization->user)->department)->department }}</small>
                                        </td>
                                        <td>{{ optional($internationalTripRealization->costCenter)->number }} - {{ optional($internationalTripRealization->costCenter)->name }}</td>
                                        <td><span class="badge badge-info">{{ $internationalTripRealization->revisions()->count() }} revisions</span></td>
                                        <td>
                                            @php $statusText = optional($internationalTripRealization->status)->status ?? 'Unknown'; @endphp
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
                                        </td>
                                        <td>{{ optional($internationalTripRealization->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            @if($internationalTripRealization->hardfile_received_at)
                                            {{ $internationalTripRealization->hardfile_received_at->format('d M Y H:i') }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('accounting-staff.international-trip-realization.show', $internationalTripRealization) }}" class="btn btn-sm btn-primary">Review</a>
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

<!-- Script for Bulk Receive -->
<script>
    $(document).ready(function() {
        function updateBulkReceiveButton() {
            var selectedCount = $('.doc-checkbox:checked').length;
            $('#selected-count').text(selectedCount);
            if (selectedCount > 0) {
                $('#btn-bulk-receive').show();
            } else {
                $('#btn-bulk-receive').hide();
            }
        }

        $('#table-1').on('change', 'input[type="checkbox"]', function() {
            setTimeout(updateBulkReceiveButton, 50);
        });

        $('#btn-bulk-receive').click(function() {
            var selectedIds = [];
            $('.doc-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) return;

            swal({
                title: 'Receive Hardfile?',
                text: 'You are about to receive hardfile for ' + selectedIds.length + ' document(s).',
                icon: 'warning',
                buttons: true,
            })
            .then((willReceive) => {
                if (willReceive) {
                    $('#bulk-inputs').empty();
                    selectedIds.forEach(function(id) {
                        $('#bulk-inputs').append('<input type="hidden" name="document_ids[]" value="' + id + '">');
                    });
                    
                    $('#bulk-receive-form').submit();
                }
            });
        });
    });
</script>
@endpush
