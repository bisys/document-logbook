@extends('layouts.app')

@section('title', 'Document')

@section('content')
<section class="section">

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Document Types</h4>
                        <div class="card-header-action">
                            <button class="btn btn-icon icon-left btn-primary" data-toggle="modal" data-target="#modal-add">
                                <i class="fa fa-plus"></i>
                                Add
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="table-1">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Full Name</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($documentTypes as $documentType)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $documentType->name }}</td>
                                        <td>{{ $documentType->full_name }}</td>
                                        <td>{{ $documentType->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $documentType->updated_at->format('d-m-Y H:i:s') }}</td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#modal-edit-{{ $documentType->slug }}" class="btn btn-icon icon-left btn-primary">
                                                <i class="far fa-edit"></i>
                                                Edit
                                            </a>
                                            <a href="{{ route('document-type.destroy', $documentType->slug) }}" id="delete" class="btn btn-icon icon-left btn-danger">
                                                <i class="fas fa-times"></i>
                                                Delete
                                            </a>
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

<div class="modal fade" tabindex="-1" role="dialog" id="modal-add">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('document-type.store') }}" method="post" id="form-add">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Document Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="e.g. SPR" name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="e.g. Supplier Payment Request" name="full_name" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" id="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($documentTypes as $documentType)
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-{{ $documentType->slug }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('document-type.update', $documentType->slug) }}" method="post" id="form-edit-{{ $documentType->slug }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Document Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="e.g. SPR" name="name" id="name" value="{{ $documentType->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="e.g. Supplier Payment Request" name="full_name" id="full_name" value="{{ $documentType->full_name }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-whitesmoke br">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script src="/assets/js/page/modules-datatables.js"></script>

<script>
    $(document).on("submit", "#form-add", function() {
        $("#submit").attr("disabled", "true");
    });

    $(document).on("click", "#delete", function(e) {
        e.preventDefault();
        var link = $(this).attr("href");

        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this data!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = link;
                form.innerHTML = '@csrf <input type="hidden" name="_method" value="DELETE">';
                document.body.appendChild(form);
                form.submit();
            }
        });
    });
</script>

@foreach($documentTypes as $documentType)
<script>
    $(document).on("submit", "#form-edit-{{ $documentType->slug }}", function(e) {
        e.preventDefault();

        swal({
            title: "Are you sure?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willConfirm) => {
            if (willConfirm) {
                document.forms["form-edit-{{ $documentType->slug }}"].submit();
            }
        });
    });
</script>
@endforeach

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
    iziToast.warning({
        message: '{{ session()->get("error") }}',
        position: 'topRight'
    });
</script>
@endif

@endpush