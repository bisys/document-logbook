@extends('layouts.app')

@section('title', 'Edit Supplier Payment')

@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back">
            <a href="{{ route('user.supplier-payment.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
        </div>
        <h1>Edit Supplier Payment</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">All Supplier Payment</a></div>
            <div class="breadcrumb-item">Edit Supplier Payment</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Edit Supplier Payment</h4>
                    </div>
                    <div class="card-body">
                        <!-- Info Card -->
                        <div class="alert alert-info mb-4">
                            <strong>{{ $supplierPayment->number }}</strong>
                            <span class="badge badge-info ml-2">Document: {{ $supplierPayment->document_number }}</span>
                            <span class="badge badge-info ml-2">Cost Center: {{ optional($supplierPayment->costCenter)->number }}</span>
                        </div>

                        <form action="{{ route('user.supplier-payment.update', $supplierPayment->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Number*</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="text" class="form-control" name="document_number" value="{{ $supplierPayment->document_number }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Cost Center*</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="cost_center_id" required>
                                        <option value="">-- Select Cost Center --</option>
                                        @foreach($costCenters as $costCenter)
                                        <option value="{{ $costCenter->id }}" {{ $supplierPayment->cost_center_id == $costCenter->id ? 'selected' : '' }}>{{ $costCenter->number }} - {{ $costCenter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">SPR Form*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->spr_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->spr_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->spr_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="spr_form" class="custom-file-input" id="spr-form" {{ empty($supplierPayment->spr_form) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->spr_form) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Original Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->original_invoice))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->original_invoice) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->original_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="original_invoice" class="custom-file-input" id="original-invoice" {{ empty($supplierPayment->original_invoice) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->original_invoice) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Copy Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->copy_invoice))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->copy_invoice) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->copy_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="copy_invoice" class="custom-file-input" id="copy-invoice" {{ empty($supplierPayment->copy_invoice) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->copy_invoice) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Tax Invoice*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->tax_invoice))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->tax_invoice) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->tax_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="tax_invoice" class="custom-file-input" id="tax-invoice" {{ empty($supplierPayment->tax_invoice) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->tax_invoice) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Budget Plan*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->budget_plan))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->budget_plan) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->budget_plan) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="budget_plan" class="custom-file-input" id="budget-plan" {{ empty($supplierPayment->budget_plan) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->budget_plan) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Agreement / Quotation*</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->agreement))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->agreement) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->agreement) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="agreement" class="custom-file-input" id="agreement" {{ empty($supplierPayment->agreement) ? 'required' : '' }}>
                                        <label class="custom-file-label">{{ empty($supplierPayment->agreement) ? 'Choose File' : 'Replace File (Optional)' }}</label>
                                    </div>
                                    <div class="form-text text-muted">The file must have a maximum size of 500KB</div>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Internal Memo Entertain</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->internal_memo_entertain))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->internal_memo_entertain) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->internal_memo_entertain) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
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
                                    @if(!empty($supplierPayment->entertain_realization_form))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->entertain_realization_form) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->entertain_realization_form) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
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
                                    @if(!empty($supplierPayment->minutes_of_meeting))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->minutes_of_meeting) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->minutes_of_meeting) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
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
                                    @if(!empty($supplierPayment->nominative_summary))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->nominative_summary) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->nominative_summary) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
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
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Calculation Summary</label>
                                <div class="col-sm-12 col-md-7">
                                    @if(!empty($supplierPayment->calculation_summary))
                                    <div class="card card-sm mb-2 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-muted">Current File:</small><br>
                                                    <strong>{{ basename($supplierPayment->calculation_summary) }}</strong>
                                                </div>
                                                <a href="{{ asset('storage/' . $supplierPayment->calculation_summary) }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                                            </div>
                                        </div>
                                    </div>
                                    <small class="text-muted">Upload a new file to replace it (optional)</small>
                                    @endif
                                    <div class="custom-file">
                                        <input type="file" name="calculation_summary" class="custom-file-input" id="calculation-summary">
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