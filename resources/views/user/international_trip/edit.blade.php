@extends('layouts.app')
@section('title', 'Edit International Trip')
@section('content')
<section class="section">
    <div class="section-header">
        <div class="section-header-back"><a href="{{ route('user.international-trip.show', $internationalTrip) }}" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a></div>
        <h1>Edit International Trip</h1>
    </div>
    <div class="section-body"><div class="row"><div class="col-12"><div class="card">
        <div class="card-header"><h4>Edit International Trip: {{ $internationalTrip->number }}</h4></div>
        <div class="card-body">
            <form action="{{ route('user.international-trip.update', $internationalTrip) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="form-group"><label>Cost Center <span class="text-danger">*</span></label><select class="form-control" name="cost_center_id" required>@foreach($costCenters as $cc)<option value="{{ $cc->id }}" {{ $internationalTrip->cost_center_id==$cc->id?'selected':'' }}>{{ $cc->number }} - {{ $cc->name }}</option>@endforeach</select></div><div class="form-group"><label>Document Number <span class="text-danger">*</span></label><input type="text" class="form-control" name="document_number" value="{{ $internationalTrip->document_number }}" required></div><div class="form-group"><label>ITAR Form</label>@if($internationalTrip->itar_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->itar_form) }}</small></div>@endif<input type="file" class="form-control-file" name="itar_form"></div><div class="form-group"><label>Internal Memo</label>@if($internationalTrip->internal_memo)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->internal_memo) }}</small></div>@endif<input type="file" class="form-control-file" name="internal_memo"></div><div class="form-group"><label>Summary Business Trip</label>@if($internationalTrip->summary_bussiness_trip)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->summary_bussiness_trip) }}</small></div>@endif<input type="file" class="form-control-file" name="summary_bussiness_trip"></div><div class="form-group"><label>Overseas Allowance Form</label>@if($internationalTrip->overseas_allowance_form)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->overseas_allowance_form) }}</small></div>@endif<input type="file" class="form-control-file" name="overseas_allowance_form"></div><div class="form-group"><label>Business Trip Allowance</label>@if($internationalTrip->bussiness_trip_allowance)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->bussiness_trip_allowance) }}</small></div>@endif<input type="file" class="form-control-file" name="bussiness_trip_allowance"></div><div class="form-group"><label>Rate</label>@if($internationalTrip->rate)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->rate) }}</small></div>@endif<input type="file" class="form-control-file" name="rate"></div><div class="form-group"><label>Budget Plan</label>@if($internationalTrip->budget_plan)<div class="mb-2"><small class="text-muted">Current: {{ basename($internationalTrip->budget_plan) }}</small></div>@endif<input type="file" class="form-control-file" name="budget_plan"></div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
                    <a href="{{ route('user.international-trip.show', $internationalTrip) }}" class="btn btn-light">Cancel</a>
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