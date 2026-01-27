@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<section class="section">

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>User Management</h4>
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
                                        <th>Employee ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->employee_id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->department->department }}</td>
                                        <td>{{ $user->position->position }}</td>
                                        <td>{{ $user->role_id == 1 ? 'Admin' : ($user->role_id == 2 ? 'Accounting' : 'User') }}</td>
                                        <td>
                                            <a href="#" data-toggle="modal" data-target="#modal-edit-{{ $user->slug }}" class="btn btn-icon icon-left btn-primary">
                                                <i class="far fa-edit"></i>
                                                Edit
                                            </a>
                                            <a href="{{ route('user.destroy', $user->slug) }}" id="delete" class="btn btn-icon icon-left btn-danger">
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
            <form action="{{ route('user.store') }}" method="post" id="form-add">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="employee_id" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Type here..." name="email" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Type here..." name="password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select name="department_id" id="" class="form-control">
                            <option value="" disabled selected>-- Select Department --</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_id">Position</label>
                        <select name="position_id" id="" class="form-control">
                            <option value="" disabled selected>-- Select Position --</option>
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}">{{ $position->position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" id="" class="form-control">
                            <option value="" disabled selected>-- Select Role --</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->role }}</option>
                            @endforeach
                        </select>
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

@foreach($users as $user)
<div class="modal fade" tabindex="-1" role="dialog" id="modal-edit-{{ $user->slug }}">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('user.update', $user->slug) }}" method="post" id="form-edit-{{ $user->slug }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="employee_id" id="employee_id" value="{{ $user->employee_id }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Type here..." name="name" id="name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Type here..." name="email" id="email" value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="department_id">Department</label>
                        <select name="department_id" id="" class="form-control">
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>{{ $department->department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position_id">Position</label>
                        <select name="position_id" id="" class="form-control">
                            @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ $user->position_id == $position->id ? 'selected' : '' }}>{{ $position->position }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="role_id">Role</label>
                        <select name="role_id" id="" class="form-control">
                            <option value="1" {{ $user->role_id == 1 ? 'selected' : '' }}>Admin</option>
                            <option value="2" {{ $user->role_id == 2 ? 'selected' : '' }}>Accounting</option>
                            <option value="3" {{ $user->role_id == 3 ? 'selected' : '' }}>User</option>
                        </select>
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

@foreach($users as $user)
<script>
    $(document).on("submit", "#form-edit-{{ $user->slug }}", function(e) {
        e.preventDefault();

        swal({
            title: "Are you sure?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willConfirm) => {
            if (willConfirm) {
                document.forms["form-edit-{{ $user->slug }}"].submit();
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