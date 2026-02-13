@extends('layouts.app')

@section('title', 'Approval Statuses')

@section('content')
<section class="section">

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Approval Statuses</h4>
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
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvalStatuses as $approvalStatus)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $approvalStatus->status }}</td>
                                        <td>{{ $approvalStatus->created_at->format('d-m-Y H:i:s') }}</td>
                                        <td>{{ $approvalStatus->updated_at->format('d-m-Y H:i:s') }}</td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#modal-edit-{{ $approvalStatus->slug }}" class="btn btn-icon icon-left btn-primary">
                                                <i class="far fa-edit"></i>
                                                Edit
                                            </a>
                                            <a href="{{ route('approval-status.destroy', $approvalStatus->slug) }}" id="delete" class="btn btn-icon icon-left btn-danger">
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
            <form action="{{ route('approval-status.store') }}" method="post" id="form-add">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Approval Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="status" required>
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

@foreach($approvalStatuses as $approvalStatus)
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-{{ $approvalStatus->slug }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('approval-status.update', $approvalStatus->slug) }}" method="post" id="form-edit-{{ $approvalStatus->slug }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Approval Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="status" id="status" value="{{ $approvalStatus->status }}">
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

@foreach($approvalStatuses as $approvalStatus)
<script>
    $(document).on("submit", "#form-edit-{{ $approvalStatus->slug }}", function(e) {
        e.preventDefault();

        swal({
            title: "Are you sure?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willConfirm) => {
            if (willConfirm) {
                document.forms["form-edit-{{ $approvalStatus->slug }}"].submit();
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