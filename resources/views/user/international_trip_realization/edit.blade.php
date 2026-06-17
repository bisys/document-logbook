@extends('layouts.app')

@section('title', 'Edit International Trip Realization')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.international-trip-realization.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit International Trip Realization</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All International Trip Realization</a></div>
            <div class="breadcrumb-item">Edit International Trip Realization</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit International Trip Realization</h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <strong>{{ $internationalTripRealization->number }}</strong>
                            <span class="badge badge-info ml-2">Document: {{ $internationalTripRealization->trip->document_number }}</span>
                            <span class="badge badge-info ml-2">Cost Center: {{ optional($internationalTripRealization->costCenter)->number }}</span>
                        </div>

                        <form action="{{ route('user.international-trip-realization.update', $internationalTripRealization->id) }}" method="POST" enctype="multipart/form-data" id="edit-form">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Transfer Evidence*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTripRealization->transfer_evidence))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTripRealization->transfer_evidence) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTripRealization->transfer_evidence) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="transfer_evidence" class="custom-file-input" id="transfer-evidence" {{ empty($internationalTripRealization->transfer_evidence) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($internationalTripRealization->transfer_evidence) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 5MB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Other Document</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($internationalTripRealization->other_document))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($internationalTripRealization->other_document) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $internationalTripRealization->other_document) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="other_document" class="custom-file-input" id="other-document">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 15MB</div>
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
    document.querySelectorAll('.custom-file-input').forEach(function(input) {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose File';
            const label = this.nextElementSibling;
            if (label && label.classList.contains('custom-file-label')) {
                label.textContent = fileName;
            }
        });
    });

    $('#edit-form').on('submit', function() {
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Submitting...');
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
