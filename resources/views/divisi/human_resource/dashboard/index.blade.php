@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/switchery/switchery.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
    label {
        font-weight: bold;
    }

    .ibox-footer {
        border: 1px solid #e7eaec;
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
        <div class="row mb-3">
            <div class="col-md-3">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <h5>Karwayan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="emp_total" title="Total Karyawan">0</label>
                        </h1>
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <small>Total Karyawan</small>
                            <div class="stat-percent font-bold text-warning"></div>
                        </div>
                    </div>
                    <div class="ibox-footer" style="border: 1px solid #e7eaec;">
                        <a href="#showEmployee" onclick="showModal('modal_emp', '', '')">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <h5>Pengajuan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="pgj_total" title="Total Pengajuan">0</label>
                        </h1>
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <small>Total Pengajuan</small>
                            <small><div class="stat-percent font-bold text-warning" id="pgj_confirmation_text"></div></small>
                        </div>
                    </div>
                    <div class="ibox-footer" style="border: 1px solid #e7eaec;">
                        <a href="#showPengajuan" onclick="showModal('modal_pgj','','')">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-primary float-right" id="abs_curr_date">@php echo date('Y-m-d') @endphp</span>
                        </div>
                        <h5 class="no-margins">Absensi</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="abs_total" title="Total Absensi Hari Ini">0</label>
                        </h1>
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <small class="text-white"><label class="font-weight-normal no-margins">test</label></small>
                        </div>
                    </div>
                    <div class="ibox-footer" style="border: 1px solid #e7eaec;">
                        <a href="#showAbsensi" onclick="showModal('modal_abs', '', '')">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <h5>Pengajuan Lembur</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="pgj_lmb_total" title="Total Pengajuan Lembur">0</label>
                        </h1>
                        <div class="d-flex flex-row align-items-center justify-content-between w-100">
                            <small>Total Pengajuan</small>
                            <small><div class="stat-percent font-bold text-warning" id="pgj_lmb_confirmation_text"></div></small>
                        </div>
                    </div>
                    <div class="ibox-footer" style="border: 1px solid #e7eaec;">
                        <a href="#showPengajuanLembur" onclick="showModal('modal_pgj_lmb','','')">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_pgj">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="modal-title no-margins">List Pengajuan</h4>
                        <button class="close" onclick="closeModal('modal_pgj')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-borderless" style="width: 100%;" id="table_list_pengajuan">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" style="width: 5%;">No</th>
                                    <th class="text-center align-middle" style="width: 15%;">Nama Pengaju</th>
                                    <th class="text-center align-middle" style="width: 20%;">Tgl. Pengajuan</th>
                                    <th class="text-center align-middle">Uraian</th>
                                    <th class="text-center align-middle" style="width: 5%;">Jenis</th>
                                    <th class="text-center align-middle" style="width: 5%;">Status</th>
                                    <th class="text-center align-middle" style="width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_abs">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins modal-title">Table List Absensi</h4>
                        <button class="close" onclick="closeModal('modal_abs')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Tanggal Cari</label>
                        </div>
                        <div class="col-sm-3">
                            <label>User</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="abs_tgl_cari" name="abs_tgl_cari" placeholder="DD/MM/YYYY s/d DD/MM/YYYY" readonly style="background: white; cursor: pointer; height: 38px;">
                        </div>
                        <div class="col-sm-3">
                            <select id="abs_user_cari" name="abs_user_cari" class="form-control" style="width: 100%;"></select>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" title="Cari Data" style="height: 38px;" onclick="showData('table_list_absensi')">Cari</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-sm btn-primary" onclick="showData('download_data_excel')">
                                <i class="fa fa-file-excel-o"></i>&nbsp;Download File Excel
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-striped" style="width: 100%;" id="table_list_absensi">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 10%;">Tanggal</th>
                                        <th class="text-left align-middle">Nama</th>
                                        <th class="text-center align-middle" style="width: 15%;">Jam Masuk</th>
                                        <th class="text-center align-middle" style="width: 15%;">Jam Keluar</th>
                                        <th class="text-center align-middle" style="width: 15%;">Telat Jam</th>
                                        <th class="text-center align-middle" style="width: 15%;">Lebih Jam</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total : </th>
                                        <th id="table_list_absensi_total_jam_masuk"></th>
                                        <th id="table_list_absensi_total_jam_keluar"></th>
                                        <th id="table_list_absensi_total_jam_telat"></th>
                                        <th id="table_list_absensi_total_jam_lebih"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_emp">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">List Karyawan</h4>
                        <button class="close" onclick="closeModal('modal_emp')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table_emp" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%;">No</th>
                                            <th class="text-center align-middle">Nama</th>
                                            <th class="text-center align-middle" style="width: 25%;">Divisi</th>
                                            <th class="text-center align-middle" style="wdith: 20%;">Role</th>
                                            <th class="text-center align-middle" style="width: 10%;">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_pgj_lmb">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">List Pengajuan Lemburan</h4>
                        <button type="button" class="close" onclick="closeModal('modal_pgj_lmb')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="table_pgj_lmb" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Nama Pengaju</th>
                                            <th class="text-center align-middle">Tgl. Pengajuan</th>
                                            <th class="text-center align-middle">Status</th>
                                            <th class="text-center align-middle">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_pgj_lmb_preview">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">Pengajuan Lembur</h4>
                        <button class="close" type="button" onclick="closeModal('modal_pgj_lmb_preview')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label>Nama</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control" id="pgj_lmb_act_id" readonly placeholder="Pengajuan Lembur ID">
                            <input type="text" class="form-control" id="pgj_lmb_user_name" readonly placeholder="Nama">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label>Bagian</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pgj_lmb_user_division" readonly placeholder="Bagian">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label>Untuk</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="pgj_lmb_act_desc" readonly placeholder="Untuk">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-bordered" style="width: 100%;" id="tbl_pgj_lmb_preview">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal</th>
                                            <th class="text-left align-middle">Uraian Pekerjaan</th>
                                            <th class="text-center align-middle">Jam Mulai</th>
                                            <th class="text-center align-middle">Jam Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="pgj_lmb_btn_tolak" onclick="doSimpan('pengajuan_lembur', 'tolak', '')">Tolak</button>
                    <button class="btn btn-primary" id="pgj_lmb_btn_terima" onclick="doSimpan('pengajuan_lembur', 'terima', '')">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('assets/js/plugins/switchery/switchery.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/human_resource/dashboard/index.hr.dashboard.js') }}"></script>
@endpush