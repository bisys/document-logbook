@extends('layouts.app')

@section('title', 'Create International Trip Realization')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.international-trip-realization.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Create International Trip Realization</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All International Trip Realization</a></div>
            <div class="breadcrumb-item">Create International Trip Realization</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>International Trip Realization Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.international-trip-realization.store') }}" method="POST" enctype="multipart/form-data" id="form">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">International Trip*</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="international_trip_id" required>
                                        <option value="">-- Select Fully Approved International Trip --</option>
                                        @foreach($availableTrips as $trip)
                                        <option value="{{ $trip->id }}" @if(old('international_trip_id') == $trip->id) selected @endif>Number : <b>{{ $trip->number }}</b> - Document Number : <b>{{ $trip->document_number }}</b></option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Transfer Evidence*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="transfer_evidence" class="custom-file-input" id="transfer-evidence" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 5MB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Other Document</label>
                                <div class="col-sm-12 col-md-7">
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
