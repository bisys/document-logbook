@extends('layouts.app')

@section('title', 'Report Export')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Report Export</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Report</div>
        </div>
    </div>

    <div class="section-body">

        <div class="row">
            <div class="col-12 col-md-8 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Report Filters</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ url(request()->segment(1) . '/report/export') }}" method="POST" id="reportForm">
                            @csrf
                            <input type="hidden" name="export_type" id="export_type" value="excel">
                            
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Type</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2" name="document_type_id">
                                        <option value="all">All Document Type</option>
                                        @foreach($documentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Department</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2" name="department_id">
                                        <option value="all">All Department</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->department }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Document Status</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control select2" name="document_status_id">
                                        <option value="all">All Status</option>
                                        @foreach($documentStatuses as $status)
                                            <option value="{{ $status->id }}">{{ $status->status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Has Revision?</label>
                                <div class="col-sm-12 col-md-7">
                                    <select class="form-control selectric" name="has_revision">
                                        <option value="all">All</option>
                                        <option value="yes">Yes, has revision</option>
                                        <option value="no">No revision</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">Start Date</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3">End Date</label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"></label>
                                <div class="col-sm-12 col-md-7">
                                    <button type="button" class="btn btn-danger mr-2" onclick="submitExport('pdf')">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="submitExport('excel')">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </button>
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
    function submitExport(type) {
        document.getElementById('export_type').value = type;
        
        var form = document.getElementById('reportForm');
        
        // Validation for dates
        var startDate = form.elements['start_date'].value;
        var endDate = form.elements['end_date'].value;
        
        if (startDate && endDate && startDate > endDate) {
            iziToast.error({
                message: 'End date must be same or after start date',
                position: 'topRight'
            });
            return;
        }

        form.submit();
    }
</script>
@if($errors->any())
@foreach ($errors->all() as $error)
<script>
        iziToast.error({   
            message: '{{ $error }}',
            position: 'topRight'
        });
    </script>
@endforeach
@endif
@endpush
