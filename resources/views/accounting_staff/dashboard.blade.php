@extends('layouts.app')

@section('title', 'Accounting Staff Dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Accounting Staff Dashboard</h1>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-danger text-white">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Need Your Action</h4>
                    </div>
                    <div class="card-body">
                        {{ $needYourAction }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning text-white">
                    <i class="fas fa-pen"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Waiting Revision</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalWaitingRevision }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Total Documents</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalDocuments }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>All System Documents by Type</h4>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled list-unstyled-border">
                        @foreach($breakdown as $type => $count)
                        <li class="media">
                            <div class="media-icon bg-primary text-white rounded-circle mr-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-file-invoice-dollar"></i>
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
        
        <div class="col-lg-6 col-md-12 col-12 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4>Actionable Items</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('accounting-staff.supplier-payment.index', ['status' => 'waiting-approval-staff']) }}" class="btn btn-primary btn-lg btn-block"><i class="fas fa-eye"></i> SP (Pending)</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('accounting-staff.petty-cash.index', ['status' => 'waiting-approval-staff']) }}" class="btn btn-info btn-lg btn-block"><i class="fas fa-eye"></i> PC (Pending)</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('accounting-staff.cash-advance-draw.index', ['status' => 'waiting-approval-staff']) }}" class="btn btn-warning btn-lg btn-block"><i class="fas fa-eye"></i> CA Draw (Pending)</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('accounting-staff.cash-advance-realization.index', ['status' => 'waiting-approval-staff']) }}" class="btn btn-success btn-lg btn-block"><i class="fas fa-eye"></i> CA Realization (Pending)</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection