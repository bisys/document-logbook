@extends('layouts.app')
@section('title', 'Create Cash Advance Draw')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.cash-advance-draw.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Create Cash Advance Draw</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>New Cash Advance Draw</h4></div>
        <div class="card-body">
            <form action="{{ route('user.cash-advance-draw.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control @error('cost_center_id') is-invalid @enderror" name="cost_center_id" required><option value="">Select Cost Center</option>@foreach($costCenters as $cc)<option value="{{ $cc->id }}">{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select>@error('cost_center_id')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control @error('document_number') is-invalid @enderror" name="document_number" value="{{ old('document_number') }}" required>@error('document_number')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>CAR Form <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('car_form') is-invalid @enderror" name="car_form" required>@error('car_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Proposal / Monitor Budget <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('proposal_or_monitor_budget') is-invalid @enderror" name="proposal_or_monitor_budget" required>@error('proposal_or_monitor_budget')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Budget Plan <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('budget_plan') is-invalid @enderror" name="budget_plan" required>@error('budget_plan')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                    <a href="{{ route('user.cash-advance-draw.index') }}" class="btn btn-light">Cancel</a>
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