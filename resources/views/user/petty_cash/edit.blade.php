@extends('layouts.app')
@section('title', 'Edit Petty Cash')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.petty-cash.show', $pettyCash) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Edit Petty Cash</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>Edit Petty Cash: {{ $pettyCash->number }}</h4></div>
        <div class="card-body">
            <form action="{{ route('user.petty-cash.update', $pettyCash) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control" name="cost_center_id" required>@foreach($costCenters as $cc)<option value="{{ $cc->id }}" {{ $pettyCash->cost_center_id==$cc->id?'selected':'' }}>{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select></div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control" name="document_number" value="{{ $pettyCash->document_number }}" required></div><div class="form-group"><label>PCR Form</label>@if($pettyCash->pcr_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->pcr_form) }}</small></div>@endif<input type="file" class="form-control-file" name="pcr_form"></div><div class="form-group"><label>Original Invoice</label>@if($pettyCash->original_invoice)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->original_invoice) }}</small></div>@endif<input type="file" class="form-control-file" name="original_invoice"></div><div class="form-group"><label>Copy Invoice</label>@if($pettyCash->copy_invoice)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->copy_invoice) }}</small></div>@endif<input type="file" class="form-control-file" name="copy_invoice"></div><div class="form-group"><label>Internal Memo Entertain</label>@if($pettyCash->internal_memo_entertain)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->internal_memo_entertain) }}</small></div>@endif<input type="file" class="form-control-file" name="internal_memo_entertain"></div><div class="form-group"><label>Entertain Realization Form</label>@if($pettyCash->entertain_realization_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->entertain_realization_form) }}</small></div>@endif<input type="file" class="form-control-file" name="entertain_realization_form"></div><div class="form-group"><label>Minutes Of Meeting</label>@if($pettyCash->minutes_of_meeting)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->minutes_of_meeting) }}</small></div>@endif<input type="file" class="form-control-file" name="minutes_of_meeting"></div><div class="form-group"><label>Nominative Summary</label>@if($pettyCash->nominative_summary)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->nominative_summary) }}</small></div>@endif<input type="file" class="form-control-file" name="nominative_summary"></div><div class="form-group"><label>CIC Form</label>@if($pettyCash->cic_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->cic_form) }}</small></div>@endif<input type="file" class="form-control-file" name="cic_form"></div><div class="form-group"><label>Budget Plan</label>@if($pettyCash->budget_plan)<div class="mb-2"><small class="text-muted">Current: {{ basename($pettyCash->budget_plan) }}</small></div>@endif<input type="file" class="form-control-file" name="budget_plan"></div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="{{ route('user.petty-cash.show', $pettyCash) }}" class="btn btn-light">Cancel</a>
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