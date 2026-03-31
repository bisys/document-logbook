@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Users</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalUsers }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Departments</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalDepartments }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Cost Centers</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalCostCenters }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>All Documents</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalDocuments }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Documents Status Overview</h4>
                </div>
                <div class="card-body">
                    <div class="statistic-details mt-sm-4">
                        <div class="statistic-details-item">
                            <span class="text-muted"><span class="text-primary"><i class="fas fa-file-alt"></i></span></span>
                            <div class="detail-value">{{ $totalDocuments }}</div>
                            <div class="detail-name">Total Documents</div>
                        </div>
                        <div class="statistic-details-item">
                            <span class="text-muted"><span class="text-warning"><i class="fas fa-clock"></i></span></span>
                            <div class="detail-value">{{ $totalPending }}</div>
                            <div class="detail-name">Pending Processing</div>
                        </div>
                        <div class="statistic-details-item">
                            <span class="text-muted"><span class="text-success"><i class="fas fa-check-circle"></i></span></span>
                            <div class="detail-value">{{ $totalApproved }}</div>
                            <div class="detail-name">Fully Approved</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Documents by Type</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        @foreach($breakdown as $type => $count)
                        <li class="media">
                            <div class="media-icon bg-primary text-white rounded-circle mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="media-body">
                                <div class="float-right text-primary font-weight-bold">{{ $count }}</div>
                                <div class="media-title">{{ $type }}</div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection