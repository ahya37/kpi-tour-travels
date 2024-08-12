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
                        <h4 style="margin:0px;" id="card_title">List Aktivitas Harian <span id="kalender_bulan"></span></h4>
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
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/digital/index.digital.js') }}"></script>
@endpush