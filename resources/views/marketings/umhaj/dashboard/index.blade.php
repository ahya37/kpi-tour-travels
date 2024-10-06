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
                <div class="col-sm-3 mb-2">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins">Umrah</h4>
                        </div>
                        <div class="card-body text-right" id="dashboard_umrah_total_data">
                            <div class="spinner-border font-weight-normal"></div>
                        </div>
                        <a href="#show_daftar_umrah" class="card-footer" onclick="showModal('modal_list_umrah', '')">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="col-sm-3 mb-2">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins">Haji</h4>
                        </div>
                        <div class="card-body text-right" id="dashboard_haji_total_data">
                            <div class="spinner-border"></div>
                        </div>
                        <a href="#show_daftar_haji" class="card-footer">
                            Lihat Detail
                        </a>
                    </div>
                </div>
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

    <div class="modal fade" id="modal_list_umrah">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row justify-content-between align-items-center w-100">
                        <h4 class="no-margins">List Program Umrah</h4>
                        <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_list_umrah')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="table_list_umrah">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tour Code</th>
                                            <th class="text-center align-middle">Keberangkatan</th>
                                            <th class="text-center align-middle">Kepulangan</th>
                                            <th class="text-center align-middle">Pembimbing</th>
                                            <th class="text-center align-middle">Target</th>
                                            <th class="text-center align-middle">Realisasi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-center align-middle">&nbsp;</th>
                                            <th class="text-center align-middle">&nbsp;</th>
                                            <th class="text-center align-middle">&nbsp;</th>
                                            <th class="text-center align-middle">&nbsp;</th>
                                            <th class="text-right align-middle">Total : </th>
                                            <th class="text-right align-middle" id="table_list_umrah_total_target">0</th>
                                            <th class="text-right align-middle" id="table_list_umrah_total_realisasi">0</th>
                                        </tr>
                                        <tr>
                                            <th class="text-center align-middle" colspan="4">&nbsp;</th>
                                            <th class="text-right align-middle">Persentase : </th>
                                            <th class="text-right align-middle" id="table_list_umrah_persentase">0</th>
                                            <th class="text-left align-middle">%</th>
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

    <div class="modal fade" id="modal_list_umrah_detail">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center w-100 justify-conrent-between">
                        <h4 class="no-margins modal-title">Detail Tour Code : <span id="modal_list_umrah_detail_tour_code"></span></h4>
                        <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_list_umrah_detail')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row align-items-top" style="border: 1px solid red;">
                        <div class="col-sm-6">
                            <div class="row ml-2 align-items-center">
                                <div class="col-sm-4">
                                    <label class="no-margins font-weight-bold">
                                        <h4 class="no-margins">Tour Code</h4>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <label class="no-margins font-weight-normal">
                                        <h4 class="no-margins" id="umrah_list_detail_tour_code">{tour_code}</h4>
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-2 ml-2">
                                <div class="col-sm-4">
                                    <label class="no-margins font-weight-bold">
                                        <h4 class="no-margins">Tanggal</h4>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <label class="no-margins font-weight-normal">
                                        <h4 class="no-margins" id="umrah_list_detail_date">{icon_plane_depature} {depature_date} & {icon_plane_arrival} {arrival_date}</h4>
                                    </label>
                                </div>
                            </div>
                            <div class="row mt-2 ml-2">
                                <div class="col-sm-4">
                                    <label class="no-margins font-weight-bold">
                                        <h4 class="no-margins">Pembimbing</h4>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <label class="no-margins font-weight-normal">
                                        <h4 class="no-margins" id="umrah_list_detail_mentor">{icon_user} {mentor_name}</h4>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="align-items-center">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hovered" id="table_modal_list_umrah_detail">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal Daftar</th>
                                            <th class="text-center align-middle">Banyaknya</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right align-middle" colspan="2">Total :</th>
                                            <th class="text-center align-middle" id="table_modal_list_umrah_detail_total_banyaknya">0</th>
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