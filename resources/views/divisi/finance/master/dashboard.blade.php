@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">
    
    <style>
        label {
            font-weight: bold;
        }

        input[type=text] {
            height: 37.5px;
        }

        .text-primary {
            color: #1ab394;
        }
    </style>
@endpush

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $sub_title ?? '' }}</h2>
        </div>
    </div>
@endsection

@section('content')
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-xl-3 col-md-4 col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between bg-primary">
                        <h4 class="no-margins">Master COA</h4>
                        <i class="fa fa-info-circle" title="Chart of Accounts" style="cursor: pointer;"></i>
                    </div>
                    <div class="card-body text-right" id="master_coa_content">
                        <div class="spinner spinner-border"></div>
                        <h2 class="no-margins"></h2>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('finance.master.coa.index') }}" title="Lihat Detail">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-4 col-sm-6 col-12"></div>
            <div class="col-xl-3 col-md-4 col-sm-6 col-12"></div>
            <div class="col-xl-3 col-md-4 col-sm-6 col-12"></div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/master/dashboard.js') }}"></script>
@endpush