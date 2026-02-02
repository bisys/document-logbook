@extends('layouts.app')

@section('title', 'Assign Permissions')

@section('content')
<section class="section">

    <div class="section-body">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Assign Permissions to Role</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/admin/role/{{ $role->slug }}/permission">
                            @csrf
                            <h6>Role: <b>{{ $role->role }}</b></h6>
                            <div class="row">
                                @foreach($permissions as $p)
                                <div class="col-md-3">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="permissions[]"
                                            value="{{ $p->id }}"
                                            {{ in_array($p->id, $rolePermissions) ? 'checked' : '' }}
                                            id="permission-{{ $p->slug }}">
                                        <label class="custom-control-label" for="permission-{{ $p->slug }}">
                                            {{ $p->permission }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                                <div class="col-12">
                                    <button type="button" class="btn btn-secondary mt-4" onclick="window.history.back();">Back</button>
                                    <button type="submit" class="btn btn-primary mt-4">Save Changes</button>
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