@extends('layouts.app')

@section('title', 'Create International Trip')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.international-trip.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Create International Trip</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All International Trip</a></div>
            <div class="breadcrumb-item">Create International Trip</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>International Trip Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.international-trip.store') }}" method="POST" enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Number*</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="document_number" value="{{ old('document_number') }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cost Center*</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="cost_center_id" required>
                                        <option value="">-- Select Cost Center --</option>
                                        @foreach($costCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}" @if(old('cost_center_id') == $costCenter->id) selected @endif>{{ $costCenter->number }} - {{ $costCenter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">ITAR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="itar_form" class="custom-file-input" id="itar-form" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Internal Memo*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="internal_memo" class="custom-file-input" id="internal-memo" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Summary / Breakdown Bussiness Trip*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="summary_bussiness_trip" class="custom-file-input" id="summary-bussiness-trip" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Overseas Allowance Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="overseas_allowance_form" class="custom-file-input" id="overseas-allowance-form" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Bussiness Trip Allowance*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="bussiness_trip_allowance" class="custom-file-input" id="bussiness-trip-allowance" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Rate*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="rate" class="custom-file-input" id="rate" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Budget Plan*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="budget_plan" class="custom-file-input" id="budget-plan" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Other Document</label>
                                <div class="col-sm-12 col-md-7">
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

    // Prevent multiple click submit
    $('#form').on('submit', function() {
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
    });
</script>

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