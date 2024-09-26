@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/yearpicker/yearpicker.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">

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

    input[type=text] {
        height  : 37.5px;
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
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h4 class="card-title" style="margin: 0px;">Sasaran</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end py-2" id="sasaran_text">
                                    <span class="spinner-border"></span>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="{{ route('marketing.programkerja.sasaran') }}">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h4 class="card-title" style="margin: 0px;">Program</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end py-2" id="program_text">
                                    <span class="spinner-border"></span>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="#" onclick="show_modal('modalTableProgram', '', '')">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="card">
                            <div class="card-header bg-primary">
                                <h4 class="card-title" style="margin: 0px;">Jenis Pekerjaan</h4>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-end py-2" id="jpk_text">
                                    <span class="spinner-border"></span>
                                </div>
                            </div>
                            <div class="card-footer text-right">
                                <a href="#" onclick="show_modal('modalJenisPekerjaan', 'add', '')">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title no-margins">Laporan Rencana Kerja</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="font-weight-bold">Cari Data</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <select class="form-control select2" name="lrk_date_year" id="lrk_date_year" style="width: 100%;" onchange="cariData('lrk_sasaran_id', this.value)"></select>
                            </div>
                            <div class="col-sm-3">
                                <select class="form-control select2" name="lrk_sasaran_id" id="lrk_sasaran_id" style="width: 100%;"></select>
                            </div>
                            <div class="col-sm-2">
                                <button class="btn btn-primary" title="Cari Data" style="height: 38px;" onclick="cariData('lrk_tbl_sasaran', '')">Cari Data</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="lrk_v_tbl_sasaran">
                            <div class="col-sm-12">
                                <h2 class="no-margins" id="lrk_tbl_sasaran_title">List Sasaran</h2>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" style="width: 100%;" id="lrk_tbl_sasaran">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle" style="width: 8%">No</th>
                                                <th class="text-center align-middle">Bulan</th>
                                                <th class="text-center align-middle" style="width: 15%;">Target</th>
                                                <th class="text-center align-middle" style="width: 15%;">Realisasi</th>
                                                <th class="text-center align-middle" style="width: 15%;">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th class="text-right align-middle">Total :</th>
                                                <th id="lrk_tbl_total_target">0</th>
                                                <th id="lrk_tbl_total_realisasi">0</th>
                                                <th id="lrk_tbl_total_presentase">0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="lrk_v_tbl_program">
                            <div class="col-sm-12">
                                <input type="hidden" id="lrk_program_selected_month">
                                <h2 class="no-margins" id="lrk_program_title">Laporan Program Bulan </h2>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" style="width: 100%;" id="lrk_tbl_program">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="text-center align-middle">Nama Program</th>
                                                <th class="text-center align-middle">Target</th>
                                                <th class="text-center align-middle">Realisasi</th>
                                                <th class="text-center align-middle">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th>&nbsp;</th>
                                                <th class="text-right align-middle">Total : </th>
                                                <th id="lrk_tbl_program_total_target">0</th>
                                                <th id="lrk_tbl_program_total_realisasi">0</th>
                                                <th id="lrk_tbl_program_total_persentase">0</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="lrk_v_tbl_program_weekly">
                            <div class="col-sm-12">
                                <h2 class="no-margins" id="lrk_program_weekly_title">Laporan Program Harian Bulan</h2>
                                <br>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-bordered" style="width: 100%;" id="lrk_tbl_program_daily">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="text-center align-middle">Nama Program</th>
                                                <th class="text-center align-middle">Target</th>
                                                <th class="text-center align-middle">Realisasi</th>
                                                <th class="text-center align-middle">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th class="text-right align-middle">Total : </th>
                                                <th id="lrk_tbl_program_daily_total_target">0</th>
                                                <th id="lrk_tbl_program_daily_total_realisasi">0</th>
                                                <th id="lrk_tbl_program_daily_total_persentase"></th>
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
    </div>

    {{-- MDOAL LIST PROGRAM --}}
    <div class="modal fade shadow" id="modalTableProgram">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title py-2" style="margin: 0px;">List Program</h4>
                    <button type="button" class="close" onclick="close_modal('modalTableProgram');">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-sm-6"></div>
                        <div class="col-sm-6 text-right">
                            <button class="btn btn-primary" title="Tambah Data Baru" onclick="show_modal('modalProgram', 'add', '')">Tambah Data</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped table-hover" id="table_list_program" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%;">No</th>
                                            <th class="text-center align-middle" style="width: 40%;">Program</th>
                                            <th class="text-center align-middle" style="width: 20%;">Bulan</th>
                                            <th class="text-center align-middle" style="width: 15%;">Divisi</th>
                                            <th class="text-center align-middle" style="width: 5%;">Target</th>
                                            <th class="text-center align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3"></th>
                                            <th class="text-right align-middle">Total</th>
                                            <th class="text-left align-middle" id="tableProgram_total"></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="close_modal('modalTableProgram');">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL PROGRAM --}}
    <div class="modal fade shadow" id="modalProgram">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title py-2" style="margin: 0px;" id="modalProgram_title"></h4>
                    <button type="button" class="close" onclick="close_modal('modalProgram')" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h2 style="margin: 0px; padding: 0px;" id="modalProgram_header"></h2>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group" style="display: none;">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Program ID</label>
                                <input type="text" name="program_ID" id="program_ID" class="form-control form-control-sm" style="height: 37.5px;" placeholder="Program ID" readonly>
                            </div>
                        </div>
                    </div>
					<div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Bulan</label>
                                <select name="program_bulan" id="program_bulan" class="form-control form-control-sm" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
					<div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Program Kerja Divisi</label>
                                <select name="program_sasaranHeaderID" id="program_sasaranHeaderID" class="form-control form-control-sm" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Sasaran</label>
                                <select name="program_sasaranID" id="program_sasaranID" class="form-control form-control-sm" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Program</label> 
                                {{-- <input type="text" class="form-control form-control-sm" id="program_title" name="program_title" style="height: 37.5px;" placeholder="Uraian" autocomplete="off"> --}}
                                <select name="program_title" id="program_title" class="form-control form-control-sm" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Jenis Pekerjaan</label>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover table-bordered table-striped" id="table_jenis_pekerjaan" style="width: 100%; padding: 0px;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center align-middle">&nbsp;</th>
                                                        <th class="text-center align-middle">No</th>
                                                        <th class="text-center align-middle">Uraian</th>
                                                        <th class="text-center align-middle">Target</th>
                                                        <th class="text-center align-middle">PIC</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                        <button type="button" class="btn btn-primary" value="1" onclick="tambah_baris('table_jenis_pekerjaan','')" id="btnTambahData">Tambah Baris</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="justify-content: flex-end;">
                    <button type="button" class="btn btn-danger" id="btnHapus" onclick="hapus_data('modalProgram')">Hapus Data</button>
                    <button type="button" class="btn btn-secondary" id="btnCancel" onclick="close_modal('modalProgram')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="do_simpan('modalProgram', this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailProgram">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="modalDetailProgram_title"></h4>
                    <button class="close" onclick="close_modal('modalDetailProgram')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-sm-6">
									 <div class="row mb-2">
                                            <div class="col-sm-4 pt-2">
                                                <label>Sasaran</label>
                                            </div>
                                            <div class="col-sm-8 pt-2">
                                                <span id="modalDetailProgram_programTitle"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 pt-2">
                                                <label>Program</label>
                                            </div>
                                            <div class="col-sm-8 pt-2">
                                                <span id="modalDetailProgram_programKategori"></span>
                                            </div>
                                        </div>
                                       
                                        <div class="row mb-2">
                                            <div class="col-sm-4 pt-2">
                                                <label>Bulan</label>
                                            </div>
                                            <div class="col-sm-8 pt-2">
                                                <span id="modalDetailProgram_programBulan"></span>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <div class="col-sm-4 pt-2">
                                                <label>Divisi</label>
                                            </div>  
                                            <div class="col-sm-8 pt-2">
                                                <span id="modalDetailProgram_programDivisi"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-bodered table-hover table-striped" id="modalDetailProgram_table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-left align-middle">Jenis Pekerjaan</th>
                                            <th class="text-center align-middle">Target</th>
                                            <th class="text-center align-middle">Realisasi</th>
                                            <th class="text-left align-middle">Persentase (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th class="text-right align-middle">Total</th>
                                            <th id="modalDetailProgram_table_totalTarget"></th>
                                            <th id="modalDetailProgram_table_totalRealisasi"></th>
                                            <th id="modalDetailProgram_table_totalPercent"></th>
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

    {{-- MODAL JENIS PEKERJAAN --}}
    <div class="modal fade" id="modalJenisPekerjaan">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <h4 class="modal-title">List Jenis Pekerjaan <span id="modalJenisPekerjaanTitle"></span></h4>
                            <button type="button" class="close" onclick="close_modal('modalJenisPekerjaan')">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="d-flex flex-row align-items-center">    
                            <div id="calendar" class="w-100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTransJenisPekerjaan">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalJenisPekerjaan_title">Tambah Data Jenis Pekerjaan</h4>
                    <button class="close" onclick="close_modal('modalTransJenisPekerjaan')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan ID</label>
                                <input type="text" class="form-control form-control-sm" id="jpk_ID" name="jpk_ID" placeholder="ID" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program</label>
                                <select name="jpk_programID" id="jpk_programID" style="width: 100%;" class="form-control form-control-sm" onchange="show_select('jpk_programDetail', this.value, '', true)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <select name="jpk_programDetail" id="jpk_programDetail" style="width: 100%;" class="form-control form-control-sm"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Awal Aktivitas</label>
                                <input type="hidden" name="jpk_date" id="jpk_date">
                                <input type="text" class="form-control form-control-sm waktu" id="jpk_start_time" name="jpk_start_time" readonly style="background: white; cursor: pointer">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Akhir Aktivitas</label>
                                <input type="text" class="form-control form-control-sm waktu" id="jpk_end_time" name="jpk_end_time" readonly style="background: white; cursor: pointer">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <input type="text" class="form-control form-control-sm" id="jpk_title" name="jpk_title" placeholder="Uraian" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Banyaknya Aktivitas</label>
                                <input type="text" class="form-control" id="jpk_aktivitas" name="jpk_aktivitas" placeholder="Banyaknya Aktivitas" inputmode="numeric" style="height: 37.5px;" value="1">
                                <small style="color: #dc3545">Note : Diisi dengan banyaknya aktivitas yang akan dikerjakan</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <textarea name="jpk_description" id="jpk_description" placeholder="Tulis Deskripsi Pekerjaan" rows="4" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="jpk_btnHapus" onclick="hapus_data('modalTransJenisPekerjaan')" disabled>Hapus Data</button>
                    <button class="btn btn-secondary" onclick="close_modal('modalTransJenisPekerjaan')">Batal</button>
                    <button class="btn btn-primary" id="jpk_btnSimpan" onclick="do_simpan('modalTransJenisPekerjaan', this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_tbl_program_daily">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="no-margins modal-title">Detail Laporan Harian Bulan</h4>
                    <button class="close" onclick="close_modal('modal_tbl_program_daily')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" style="width: 100%;" id="tbl_program_daily_detail">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Penanggung Jawab</th>
                                            <th>Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th class="text-right align-middle" colspan="2">Total :</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
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
    <script src="{{ asset('js/marketings/programKerja/dashboard/index.dashboard.js') }}"></script>
@endpush