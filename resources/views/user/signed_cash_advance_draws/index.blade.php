@extends('layouts.app')

@section('title', 'Signed Cash Advance Draws')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Signed Cash Advance Draws</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="#">Dashboard</a></div>
            <div class="breadcrumb-item">Signed Cash Advance Draws</div>
        </div>
    </div>

    <div class="section-body">
        <h2 class="section-title">List File Signed Cash Advance Draw</h2>
        <p class="section-lead">
            Download Cash Advance Draw that already signed by accounting Dept.
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
@endpush
