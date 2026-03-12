@extends('layouts.app')
@section('title', 'Edit Cash Advance Realization')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.cash-advance-realization.show', $cashAdvanceRealization) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Edit Cash Advance Realization</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>Edit Cash Advance Realization: {{ $cashAdvanceRealization->number }}</h4></div>
        <div class="card-body">
            <form action="{{ route('user.cash-advance-realization.update', $cashAdvanceRealization) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group"><label>CAR Form</label>@if($cashAdvanceRealization->car_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->car_form) }}</small></div>@endif<input type="file" class="form-control-file" name="car_form"></div><div class="form-group"><label>Original Invoice</label>@if($cashAdvanceRealization->original_invoice)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->original_invoice) }}</small></div>@endif<input type="file" class="form-control-file" name="original_invoice"></div><div class="form-group"><label>Copy Invoice</label>@if($cashAdvanceRealization->copy_invoice)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->copy_invoice) }}</small></div>@endif<input type="file" class="form-control-file" name="copy_invoice"></div><div class="form-group"><label>Internal Memo Entertain</label>@if($cashAdvanceRealization->internal_memo_entertain)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->internal_memo_entertain) }}</small></div>@endif<input type="file" class="form-control-file" name="internal_memo_entertain"></div><div class="form-group"><label>Entertain Realization Form</label>@if($cashAdvanceRealization->entertain_realization_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->entertain_realization_form) }}</small></div>@endif<input type="file" class="form-control-file" name="entertain_realization_form"></div><div class="form-group"><label>Minutes Of Meeting</label>@if($cashAdvanceRealization->minutes_of_meeting)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->minutes_of_meeting) }}</small></div>@endif<input type="file" class="form-control-file" name="minutes_of_meeting"></div><div class="form-group"><label>Nominative Summary</label>@if($cashAdvanceRealization->nominative_summary)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->nominative_summary) }}</small></div>@endif<input type="file" class="form-control-file" name="nominative_summary"></div><div class="form-group"><label>CIC Form</label>@if($cashAdvanceRealization->cic_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->cic_form) }}</small></div>@endif<input type="file" class="form-control-file" name="cic_form"></div><div class="form-group"><label>Budget Plan</label>@if($cashAdvanceRealization->budget_plan)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceRealization->budget_plan) }}</small></div>@endif<input type="file" class="form-control-file" name="budget_plan"></div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="{{ route('user.cash-advance-realization.show', $cashAdvanceRealization) }}" class="btn btn-light">Cancel</a>
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