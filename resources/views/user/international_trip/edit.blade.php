@extends('layouts.app')

@section('title', 'Edit International Trip')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.international-trip.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit International Trip</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All International Trip</a></div>
            <div class="breadcrumb-item">Edit International Trip</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit International Trip</h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <strong>{{ $internationalTrip->number }}</strong>
                            <span class="badge badge-info ml-2">Document: {{ $internationalTrip->document_number }}</span>
                            <span class="badge badge-info ml-2">Cost Center: {{ optional($internationalTrip->costCenter)->number }}</span>
                        </div>

                        <form action="{{ route('user.international-trip.update', $internationalTrip->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Number*</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="document_number" value="{{ $internationalTrip->document_number }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cost Center*</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="cost_center_id" required>
                                        <option value="">-- Select Cost Center --</option>
                                        @foreach($costCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}" {{ $internationalTrip->cost_center_id == $costCenter->id ? 'selected' : '' }}>{{ $costCenter->number }} - {{ $costCenter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">ITAR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->itar_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->itar_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->itar_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="itar_form" class="custom-file-input" id="itar-form" {{ empty($internationalTrip->itar_form) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->itar_form) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Internal Memo*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->internal_memo))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->internal_memo) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->internal_memo) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="internal_memo" class="custom-file-input" id="internal-memo" {{ empty($internationalTrip->internal_memo) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->internal_memo) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Summary / Breakdown Bussiness Trip*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->summary_bussiness_trip))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->summary_bussiness_trip) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->summary_bussiness_trip) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="summary_bussiness_trip" class="custom-file-input" id="summary-bussiness-trip" {{ empty($internationalTrip->summary_bussiness_trip) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->summary_bussiness_trip) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Overseas Allowance Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->overseas_allowance_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->overseas_allowance_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->overseas_allowance_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="overseas_allowance_form" class="custom-file-input" id="overseas-allowance-form" {{ empty($internationalTrip->overseas_allowance_form) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->overseas_allowance_form) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Bussiness Trip Allowance*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->bussiness_trip_allowance))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->bussiness_trip_allowance) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->bussiness_trip_allowance) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="bussiness_trip_allowance" class="custom-file-input" id="bussiness-trip-allowance" {{ empty($internationalTrip->bussiness_trip_allowance) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->bussiness_trip_allowance) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Rate*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->rate))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->rate) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->rate) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="rate" class="custom-file-input" id="rate" {{ empty($internationalTrip->rate) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->rate) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Budget Plan*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->budget_plan))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->budget_plan) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->budget_plan) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="budget_plan" class="custom-file-input" id="budget-plan" {{ empty($internationalTrip->budget_plan) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTrip->budget_plan) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Other Document</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTrip->other_document))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTrip->other_document) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTrip->other_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="other_document" class="custom-file-input" id="other-document">
                                        <label class="custom-file-label">Choose File</label>
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