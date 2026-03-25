@extends('layouts.app')

@section('title', 'Edit Cash Advance Realization')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.cash-advance-realization.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Cash Advance Realization</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All Cash Advance Realization</a></div>
            <div class="breadcrumb-item">Edit Cash Advance Realization</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Cash Advance Realization</h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <strong>{{ $cashAdvanceRealization->number }}</strong>
                            <span class="badge badge-info ml-2">Document: {{ $cashAdvanceRealization->draw->document_number }}</span>
                            <span class="badge badge-info ml-2">Cost Center: {{ optional($cashAdvanceRealization->costCenter)->number }}</span>
                        </div>

                        <form action="{{ route('user.cash-advance-realization.update', $cashAdvanceRealization->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">CAR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceRealization->car_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->car_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->car_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="car_form" class="custom-file-input" id="car-form" {{ empty($cashAdvanceRealization->car_form) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceRealization->car_form) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Original Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceRealization->original_invoice))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->original_invoice) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->original_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="original_invoice" class="custom-file-input" id="original-invoice" {{ empty($cashAdvanceRealization->original_invoice) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceRealization->original_invoice) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Copy Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceRealization->copy_invoice))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->copy_invoice) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->copy_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="copy_invoice" class="custom-file-input" id="copy-invoice" {{ empty($cashAdvanceRealization->copy_invoice) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($cashAdvanceRealization->copy_invoice) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Internal Memo Entertain</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceRealization->internal_memo_entertain))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->internal_memo_entertain) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->internal_memo_entertain) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
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
                                    @if(!empty($cashAdvanceRealization->entertain_realization_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->entertain_realization_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->entertain_realization_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
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
                                    @if(!empty($cashAdvanceRealization->minutes_of_meeting))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->minutes_of_meeting) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->minutes_of_meeting) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
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
                                    @if(!empty($cashAdvanceRealization->nominative_summary))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->nominative_summary) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->nominative_summary) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
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
                                    @if(!empty($cashAdvanceRealization->cic_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->cic_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->cic_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="cic_form" class="custom-file-input" id="cic-form">
                                        <label class="custom-file-label">Choose File</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Transfer Evidence</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($cashAdvanceRealization->transfer_evidence))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($cashAdvanceRealization->transfer_evidence) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $cashAdvanceRealization->transfer_evidence) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="transfer_evidence" class="custom-file-input" id="transfer-evidence">
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