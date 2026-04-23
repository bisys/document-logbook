@extends('layouts.app')

@section('title', 'Create Petty Cash')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.petty-cash.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Create Petty Cash</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All Petty Cash</a></div>
            <div class="breadcrumb-item">Create Petty Cash</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Petty Cash Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('user.petty-cash.store') }}" method="POST" enctype="multipart/form-data" id="form">
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
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">PCR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="pcr_form" class="custom-file-input" id="pcr-form" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Original Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="original_invoice" class="custom-file-input" id="original-invoice" required>
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Copy Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="copy_invoice" class="custom-file-input" id="copy-invoice" required>
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
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tax Invoice</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="tax_invoice" class="custom-file-input" id="tax-invoice">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Internal Memo Entertain</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="internal_memo_entertain" class="custom-file-input" id="internal-memo-entertain">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Entertain Realization Form</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="entertain_realization_form" class="custom-file-input" id="entertain-realization-form">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Minutes of Meeting</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="minutes_of_meeting" class="custom-file-input" id="minutes-of-meeting">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Nominative Summary</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="nominative_summary" class="custom-file-input" id="nominative-summary">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CIC Form</label>
                                <div class="col-sm-12 col-md-7">
                                    <div class="custom-file">
                                        <input type="file" name="cic_form" class="custom-file-input" id="cic-form">
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