@extends('layouts.app')
@section('title', 'Create Petty Cash')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.petty-cash.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Create Petty Cash</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>New Petty Cash</h4></div>
        <div class="card-body">
            <form action="{{ route('user.petty-cash.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control @error('cost_center_id') is-invalid @enderror" name="cost_center_id" required><option value="">Select Cost Center</option>@foreach($costCenters as $cc)<option value="{{ $cc->id }}">{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select>@error('cost_center_id')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control @error('document_number') is-invalid @enderror" name="document_number" value="{{ old('document_number') }}" required>@error('document_number')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>PCR Form <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('pcr_form') is-invalid @enderror" name="pcr_form" required>@error('pcr_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Original Invoice <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('original_invoice') is-invalid @enderror" name="original_invoice" required>@error('original_invoice')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Copy Invoice <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('copy_invoice') is-invalid @enderror" name="copy_invoice" required>@error('copy_invoice')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Internal Memo Entertain</label><input type="file" class="form-control-file @error('internal_memo_entertain') is-invalid @enderror" name="internal_memo_entertain">@error('internal_memo_entertain')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Entertain Realization Form</label><input type="file" class="form-control-file @error('entertain_realization_form') is-invalid @enderror" name="entertain_realization_form">@error('entertain_realization_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Minutes Of Meeting</label><input type="file" class="form-control-file @error('minutes_of_meeting') is-invalid @enderror" name="minutes_of_meeting">@error('minutes_of_meeting')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Nominative Summary</label><input type="file" class="form-control-file @error('nominative_summary') is-invalid @enderror" name="nominative_summary">@error('nominative_summary')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>CIC Form</label><input type="file" class="form-control-file @error('cic_form') is-invalid @enderror" name="cic_form">@error('cic_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Budget Plan <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('budget_plan') is-invalid @enderror" name="budget_plan" required>@error('budget_plan')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                    <a href="{{ route('user.petty-cash.index') }}" class="btn btn-light">Cancel</a>
                </div>
            </form>
        </div>
    </div></div></div></div>
</section>
@endsection
@push('scripts')
@if(session()->has('success'))<script>iziToast.success({message:'{{ session()->get("success") }}',position:'topRight'});</script>@endif
@if(session()->has('error'))<script>iziToast.warning({message:'{{ session()->get("error") }}',position:'topRight'});</script>@endif
@endpush