@extends('layouts.app')
@section('title', 'Edit Cash Advance Draw')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.cash-advance-draw.show', $cashAdvanceDraw) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Edit Cash Advance Draw</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>Edit Cash Advance Draw: {{ $cashAdvanceDraw->number }}</h4></div>
        <div class="card-body">
            <form action="{{ route('user.cash-advance-draw.update', $cashAdvanceDraw) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control" name="cost_center_id" required>@foreach($costCenters as $cc)<option value="{{ $cc->id }}" {{ $cashAdvanceDraw->cost_center_id==$cc->id?'selected':'' }}>{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select></div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control" name="document_number" value="{{ $cashAdvanceDraw->document_number }}" required></div><div class="form-group"><label>CAR Form</label>@if($cashAdvanceDraw->car_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceDraw->car_form) }}</small></div>@endif<input type="file" class="form-control-file" name="car_form"></div><div class="form-group"><label>Proposal / Monitor Budget</label>@if($cashAdvanceDraw->proposal_or_monitor_budget)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceDraw->proposal_or_monitor_budget) }}</small></div>@endif<input type="file" class="form-control-file" name="proposal_or_monitor_budget"></div><div class="form-group"><label>Budget Plan</label>@if($cashAdvanceDraw->budget_plan)<div class="mb-2"><small class="text-muted">Current: {{ basename($cashAdvanceDraw->budget_plan) }}</small></div>@endif<input type="file" class="form-control-file" name="budget_plan"></div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="{{ route('user.cash-advance-draw.show', $cashAdvanceDraw) }}" class="btn btn-light">Cancel</a>
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