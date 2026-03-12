@extends('layouts.app')
@section('title', 'Create International Trip')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.international-trip.index') }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Create International Trip</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>New International Trip</h4></div>
        <div class="card-body">
            <form action="{{ route('user.international-trip.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control @error('cost_center_id') is-invalid @enderror" name="cost_center_id" required><option value="">Select Cost Center</option>@foreach($costCenters as $cc)<option value="{{ $cc->id }}">{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select>@error('cost_center_id')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control @error('document_number') is-invalid @enderror" name="document_number" value="{{ old('document_number') }}" required>@error('document_number')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>ITAR Form <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('itar_form') is-invalid @enderror" name="itar_form" required>@error('itar_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Internal Memo <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('internal_memo') is-invalid @enderror" name="internal_memo" required>@error('internal_memo')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Summary Business Trip <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('summary_bussiness_trip') is-invalid @enderror" name="summary_bussiness_trip" required>@error('summary_bussiness_trip')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Overseas Allowance Form <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('overseas_allowance_form') is-invalid @enderror" name="overseas_allowance_form" required>@error('overseas_allowance_form')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Business Trip Allowance <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('bussiness_trip_allowance') is-invalid @enderror" name="bussiness_trip_allowance" required>@error('bussiness_trip_allowance')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Rate <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('rate') is-invalid @enderror" name="rate" required>@error('rate')<span class="invalid-feedback">{{ $message }}</span>@enderror</div><div class="form-group"><label>Budget Plan <span class="text-danger">*</span></label><input type="file" class="form-control-file @error('budget_plan') is-invalid @enderror" name="budget_plan" required>@error('budget_plan')<span class="invalid-feedback">{{ $message }}</span>@enderror</div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Submit</button>
                    <a href="{{ route('user.international-trip.index') }}" class="btn btn-light">Cancel</a>
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