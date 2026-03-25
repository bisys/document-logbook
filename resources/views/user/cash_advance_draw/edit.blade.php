@extends('layouts.app')

@section('title', 'Edit Cash Advance Draw')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.cash-advance-draw.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Cash Advance Draw</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All Cash Advance Draw</a></div>
            <div class="breadcrumb-item">Edit Cash Advance Draw</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Cash Advance Draw</h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <strong>{{ $cashAdvanceDraw->number }}</strong>
                            <span class="badge badge-info ml-2">Document: {{ $cashAdvanceDraw->document_number }}</span>
                            <span class="badge badge-info ml-2">Cost Center: {{ optional($cashAdvanceDraw->costCenter)->number }}</span>
                        </div>

                        <form action="{{ route('user.cash-advance-draw.update', $cashAdvanceDraw->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Number*</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="document_number" value="{{ $cashAdvanceDraw->document_number }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cost Center*</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="cost_center_id" required>
                                        <option value="">-- Select Cost Center --</option>
                                        @foreach($costCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}" {{ $cashAdvanceDraw->cost_center_id == $costCenter->id ? 'selected' : '' }}>{{ $costCenter->number }} - {{ $costCenter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CAR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceDraw->car_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceDraw->car_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceDraw->car_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="car_form" class="custom-file-input" id="car-form" {{ empty($cashAdvanceDraw->car_form) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceDraw->car_form) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Proposal or Monitoring Budget Estimation*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceDraw->proposal_or_monitor_budget))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceDraw->proposal_or_monitor_budget) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceDraw->proposal_or_monitor_budget) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="proposal_or_monitor_budget" class="custom-file-input" id="proposal-or-monitor-budget" {{ empty($cashAdvanceDraw->proposal_or_monitor_budget) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceDraw->proposal_or_monitor_budget) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Budget Plan*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceDraw->budget_plan))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceDraw->budget_plan) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceDraw->budget_plan) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="budget_plan" class="custom-file-input" id="budget-plan" {{ empty($cashAdvanceDraw->budget_plan) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceDraw->budget_plan) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')

<script>
    // Display selected filename when file is chosen
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose File';
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    });
</script>

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

@endpush