@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/yearpicker/yearpicker.css') }}" rel="stylesheet">

    <style>
    label {
        font-weight: bold;
    }

    .menengah { 
        display     : flex;
        align-items : center;
        justify-content: center;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
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
    <div class="container-fluid">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row align-items-center">
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins">Umrah</h4>
                        </div>
                        <div class="card-body text-right">
                            <h2 class="no-margins">0</h2>
                        </div>
                        <a href="#show_daftar_umrah" class="card-footer">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
                <div class="col-sm-3"></div>
            </div>
            <br>
            <div class="row d-flex">
                <div class="col-sm-6 mb-3">
                    <div class="card card-body">
                        <div class="row mb-2">
                            <div class="col-sm-12 text-center">
                                <h2 class="no-margins">Jumlah Pendaftar Umrah</h2>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-sm-12">
                                <select id="g_umrah_filter_package" class="form-control" style="width: 100%;" onchange="cariData('chart_umrah', this.value)"></select>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center text-center" id="chart_umrah_loading" style="height: 250px;">
                            <div class="col-sm-12">
                                <div class="spinner-border"></div><br>
                                <label class="mt-2 font-weight-bold">Data Sedang Dimuat..</label>
                            </div>
                        </div>
                        <div class="row align-items-center text-center d-none" id="chart_umrah_view">
                            <div class="col-sm-12">
                                <canvas id="chart_umrah" style="width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 mb-3">
                    <div class="card card-body">
                        <div class="row mb-2">
                            <div class="col-sm-12 text-center">
                                <h2 class="no-margins">Jumlah Member</h2>
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-sm-12">
                                <select id="g_member_filter_cs" class="form-control" style="width: 100%;" onchange="cariData('chart_member', this.value)"></select>
                            </div>
                        </div>
                        <hr>
                        <div class="row align-items-center text-center" id="chart_member_loading" style="height: 250px;">
                            <div class="col-sm-12">
                                <div class="spinner-border"></div><br>
                                <label class="mt-2 font-weight-bold">Data Sedang Dimuat..</label>
                            </div>
                        </div>
                        <div class="row align-items-center d-none" id="chart_member_view">
                            <div class="col-sm-12">
                                <canvas id="chart_member" style="width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_member">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between" style="width: 100%;">
                        <h4 class="no-margins font-weight-bold">List PIC Member Bulan : <label class="font-weight no-margins" id="modal_member_title_month"></label></h4>
                        <button class="close" onclick="closeModal('modal_member')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <h2 class="no-margins font-weight-light">List Total Member Baru per PIC</h2>
                    <div class="row mb-2 align-items-top">
                        <div class="col-sm-6">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" style="width:100%;" id="table_modal_total_member">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 16%;">No</th>
                                            <th class="text-center align-middle">Pic</th>
                                            <th class="text-center align-middle" style="width: 30%;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="text-right align-middle">Total : </th>
                                            <th id="table_modal_total_member_footer_total">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6" style="width: 100%;">
                            <canvas id="chart_modal_total_member" style="width: 100%;"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h2 class="no-margins font-weight-light">List Member Baru per PIC</h2>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-bordered" id="table_modal_member" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal</th>
                                            <th class="text-center align-middle">PIC</th>
                                            <th class="text-center align-middle">Total Data</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" clas="text-right align-middle">Total :</th>
                                            <th id="table_modal_member_footer_total" class="text-right align-middle">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_umrah">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins font-weight-bold modal-title">
                            List Pendaftar Umrah Bulan : <span id="modal_umrah_title_month"></span>
                        </h4>
                        <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_umrah')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row align-items-top">
                        <div class="col-sm-6">
                            <div class="table-responsive">
                                <table class="table table-stripped table-bordered table-hover" id="table_modal_umrah_summary" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Kategori</th>
                                            <th class="text-center align-middle">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="text-right align-middle">Total : </th>
                                            <th class="text-center align-middle" id="table_modal_umrah_summary_total">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-sm-6"></div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="table_modal_umrah" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal</th>
                                            <th class="text-center align-middle">Tour Code / Kategori</th>
                                            <th class="text-center align-middle">Banyaknya</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">&nbsp;</th>
                                            <th class="text-right align-middle">Total :</th>
                                            <th class="text-center align-middle" id="table_modal_umrah_total">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/umhaj/dashboard/index.js') }}"></script>
@endpush