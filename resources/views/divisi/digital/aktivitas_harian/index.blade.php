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
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <h4 class="no-margins" id="card_title">List Aktivitas Harian <span id="kalender_bulan"></span></h4>
                            <button class="btn btn-sm btn-primary" onclick="showModal('modal_act_user', '', '')">
                                <span class="badge badge-white" id="total_act_user"><i class='fa fa-spinner fa-spin'></i></span>
                                Lihat Aktivitas
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <input type="hidden" id="today">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="calendar_loading">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 650px;">
                                        <div class="spinner-border"></div>
                                        <label><b>Data Sedang Dimuat</b></label>
                                    </div>
                                </div>
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_form">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" id="modal_form_title" class="modal-title"></h4>
                    <button class="close" onclick="closeModal('modal_form')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2 d-none">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan ID</label>
                                <input type="text" class="form-control" id="jpk_ID" name="jpk_ID" style="height: 37.5px;" placeholder="Jenis Pekerjaan ID">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2 d-none">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tanggal</label>
                                <input type="text" class="form-control" id="jpk_date" name="jpk_date" style="height: 37.5px;" placeholder="Tanggal Aktivitas">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program</label>
                                <select name="jpk_programID" id="jpk_programID" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <select name="jpk_programDetailID" id="jpk_programDetailID" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <label>Waktu Awal Aktivitas</label>
                            <input type="text" class="form-control waktu" style="height: 37.5px; cursor: pointer; background: white;" readonly placeholder="HH:MM:SS" value="00:00:00" name="jpk_startTime" id="jpk_startTime">
                        </div>
                        <div class="col-sm-6">
                            <label>Waktu Akhir Aktivitas</label>
                            <input type="text" class="form-control waktu" style="height: 37.5px; cursor: pointer; background: white;" readonly placeholder="HH:MM:SS" value="00:00:00" name="jpk_endTime" id="jpk_endTime">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <input type="text" class="form-control" style="height: 37.5px;" placeholder="Uraian Pekerjaan" name="jpk_title" id="jpk_title" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button class="btn ml-2 btn-danger d-none" id="btnHapus" onclick="simpanData('hapus')" title="Hapus Data">Hapus</button>
                    <button class="btn ml-2 btn-secondary" id="btnCancel" onclick="closeModal('modal_form')" title="Tutup Tampilan">Tutup</button>
                    <button class="btn ml-2 btn-primary" id="btnSimpan" value="" onclick="simpanData(this.value)" title="Simpan Data">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_act_user">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">List Aktivitas ( <span id="modal_act_usert_month"></span> )</h4>
                        <button class="close" onclick="closeModal('modal_act_user')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <table class="table table-sm table-hover" id="table_list_act_user" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center align-middle" style="width: 8%;">No</th>
                                <th class="text-center align-middle" style="width: 30%;">Program</th>
                                <th class="text-center align-middle">Uraian</th>
                                <th class="text-center align-middle" style="width: 15%;">Target</th>
                                <th class="text-center align-middle" style="width: 10%;">Persentase</th>
                                <th class="text-center align-middle" style="width: 15%;">Type</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">&nbsp;</th>
                                <th class="text-right align-middle"> Total : </th>
                                <th class="text-left align-middle" id="table_list_act_user_total_target"></th>
                                <th class="text-left align-middle" id="table_list_act_user_total_persentase"></th>
                                <th>&nbsp;</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/digital/index.digital.js') }}"></script>
@endpush