@extends('layouts.app')
@section('title', 'Create Cash Advance Realization')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.cash-advance-realization.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Create Cash Advance Realization</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>New Cash Advance Realization</h4></div>
        <div class="card-body">
            <form action="{{ route('user.cash-advance-realization.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label>Linked Cash Advance Draw <span class="text-danger">*</span></label><select class="form-control" name="cash_advance_draw_id" required><option value="">-- Select Fully Approved Draw --</option>@foreach($availableDraws as $draw)<option value="{{ $draw->id }}">{{ $draw->number }} - {{ $draw->document_number }} ({{ optional($draw->costCenter)->name }})</option>@endforeach</select></div><div class="form-group"><label>CAR Form <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('car_form') is-invalid @enderror" name="car_form" required>@error('car_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Original Invoice <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('original_invoice') is-invalid @enderror" name="original_invoice" required>@error('original_invoice')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Copy Invoice <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('copy_invoice') is-invalid @enderror" name="copy_invoice" required>@error('copy_invoice')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Internal Memo Entertain</label><input type="file" class="form-control-file @error('internal_memo_entertain') is-invalid @enderror" name="internal_memo_entertain">@error('internal_memo_entertain')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Entertain Realization Form</label><input type="file" class="form-control-file @error('entertain_realization_form') is-invalid @enderror" name="entertain_realization_form">@error('entertain_realization_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Minutes Of Meeting</label><input type="file" class="form-control-file @error('minutes_of_meeting') is-invalid @enderror" name="minutes_of_meeting">@error('minutes_of_meeting')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Nominative Summary</label><input type="file" class="form-control-file @error('nominative_summary') is-invalid @enderror" name="nominative_summary">@error('nominative_summary')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>CIC Form</label><input type="file" class="form-control-file @error('cic_form') is-invalid @enderror" name="cic_form">@error('cic_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Budget Plan <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('budget_plan') is-invalid @enderror" name="budget_plan" required>@error('budget_plan')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                    <a href="{{ route('user.cash-advance-realization.index') }}" class="btn btn-light">Cancel</a>
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