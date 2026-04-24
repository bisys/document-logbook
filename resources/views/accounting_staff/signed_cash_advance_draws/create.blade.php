@extends('layouts.app')

@section('title', 'Upload Signed Cash Advance Draw')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Upload Signed Cash Advance Draw</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Upload Signed Cash Advance Draw</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">Upload File</h2>
        <p class="section-lead">
            Upload Cash Advance Draw that already full signed by Accounting Dept. You can upload multiple files at once.
        </p>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Upload Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('accounting-staff.signed-cash-advance-draws.store') }}" method="POST" enctype="multipart/form-data" id="form-upload">
                            @csrf
                            <div class="form-group">
                                <label>Pilih File PDF</label>
                                <input type="file" name="files[]" class="form-control" accept=".pdf" multiple required>
                                <small class="form-text text-muted">Allowed format: PDF. Max 500KB per file. You can select multiple files at once.</small>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Files</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="section-title">Monitoring File</h2>
        <p class="section-lead">
            List file that already uploaded.
        </p>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>List File</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>File Name</th>
                                        <th>Uploaded At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($files as $file)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $file->file_name }}</td>
                                        <td>{{ $file->created_at->format('d M Y H:i:s') }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $file->file_path) }}" class="btn btn-primary" target="_blank"><i class="fas fa-eye"></i> View</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="/assets/js/page/modules-datatables.js"></script>

<!-- prevent multiple click submit -->
<script>
    $('#form-upload').on('submit', function() {
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true);
        submitButton.html('<i class="fas fa-spinner fa-spin"></i> Uploading...');
    });
</script>   

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
    iziToast.error({
        message: '{{ session()->get("error") }}',
        position: 'topRight'
    });
</script>
@endif

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
@endpush
