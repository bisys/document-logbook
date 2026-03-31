@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>User Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>My Submissions</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalDocuments }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Pending</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalPending }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Approved</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalApproved }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Rejected / Revision</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalRejected }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Submissions by Document Type</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        @foreach($breakdown as $type => $count)
                        <li class="media">
                            <div class="media-icon bg-primary text-white rounded-circle mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="media-body">
                                <div class="float-right font-weight-bold text-primary">{{ $count }}</div>
                                <div class="media-title">{{ $type }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('user.supplier-payment.create') }}" class="btn btn-primary btn-lg btn-block"><i class="fas fa-plus"></i> New Supplier Payment</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('user.petty-cash.create') }}" class="btn btn-info btn-lg btn-block"><i class="fas fa-plus"></i> New Petty Cash</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('user.cash-advance-draw.create') }}" class="btn btn-warning btn-lg btn-block"><i class="fas fa-plus"></i> New CA Draw</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('user.international-trip.create') }}" class="btn btn-success btn-lg btn-block"><i class="fas fa-plus"></i> New Int. Trip</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection