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
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 style="margin: 0px;">RKAP</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 style="margin: 0px;">
                            <span id="act_rkap_loading">
                                <div class="spinner-border"></div>
                            </span>
                            <span id="act_rkap_text" class="d-none"></span>
                        </h2>
                    </div>
                    <a href="#rkap" class="card-footer" onclick="showModal('modal_rkap_finance', '', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 style="margin: 0px;">Aktivitas User</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 style="margin: 0px;">
                            <span id="act_user_loading"><div class="spinner-border"></div></span>
                            <span id="act_user_text" class="d-none"></span>
                        </h2>
                    </div>
                    <a href="#" class="card-footer" onclick="showModal('modal_daily_activity', '', '')">
                        Tambah Data
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins">Absensi</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins">
                            <span id="abs_loading"><div class="spinner-border"></div></span>
                            <span id="abs_text" class="d-none"></span>
                        </h2>
                    </div>
                    <a href="#showAbsensi" class="card-footer" onclick="showModal(`modal_absensi`, '', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins">Update Gaji Pokok Karyawan</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins">
                            <span id="kar_text">0</span>
                        </h2>
                        <small>Total Karyawan</small>
                    </div>
                    <a href="#UpdateGajiPokokKaryawan" class="card-footer" onclick="showModal('modal_update_gapok_karyawan', '', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins">Perhitungan Lemburan</h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins">
                            Lakukan Simulasi
                        </h2>
                        <small class="text-white"><label class="no-margins">test</label></small>
                    </div>
                    <a href="#SimulasiLemburanKaryawan" class="card-footer" onclick="showModal('modal_simulasi_lemburan', '', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_daily_activity">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="modal-title" id="modal_daily_title"></h4>
                    <button class="close" onclick="closeModal('modal_daily_activity')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2 style="margin: 0px;" id="modal_daily_calendar_title"></h2>
                        </div>
                    </div>
                    <hr>
                    <div id="calendar-loading">
                        <div class="d-flex flex-column align-items-center justify-content-center" style="height: 653px;">
                            <span class="spinner-border"></span><br>
                            <label>Tampilan Sedang Dimuat..</label>
                        </div>
                    </div>
                    <div class="row d-none" id="calendar-show">
                        <div class="col-sm-12">
                            <input type="hidden" name="current_date" id="current_date">
                            <div id="calendar" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btn_cancel" title="Tututp Tampilan" onclick="closeModal('modal_daily_activity')">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_daily_trans">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="mt-1" id="modal_daily_trans_title"></h4>
                    <button class="close" onclick="closeModal('modal_daily_trans')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2>Aktivitas Harian</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group d-none">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Aktivitas ID</label>
                                        <input type="text" class="form-control form-control-sm" name="daily_trans_jenis" id="daily_trans_jenis" placeholder="Aktivitas ID" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Kategori</label>
                                        <select name="daily_trans_category" id="daily_trans_category" class="form-control form-control-sm" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Uraian</label>
                                        <input type="text" name="daily_trans_title" id="daily_trans_title" class="form-control form-control-sm" placeholder="Uraian Aktivitas">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Tanggal Aktivitas</label>
                                        <input type="text" name="daily_trans_start_date" id="daily_trans_start_date" class="form-control form-control-sm" placeholder="DD/MM/YYYY" readonly style="background: white; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Deskripsi</label>
                                        <textarea name="daily_trans_description" id="daily_trans_description" class="form-control form-control-sm" rows="4" placeholder="Tulis Deskripsi"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div id="v_aktivitas_operasional" class="d-none">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h2>Aktivitas Operasional</h2>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group" id="v_trans_code_select">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tour Code</label>
                                            <select name="daily_trans_tour_code" id="daily_trans_tour_code" class="form-control form-control-sm" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="v_trans_code_text">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tour Code</label>
                                            <input type="text" name="daily_trans_tour_code_text" id="daily_trans_tour_code_text" class="form-control form-control-sm" placeholder="Tour Code" readonly>
                                            <input type="hidden" name="daily_trans_pkb_id" id="daily_trans_pkb_id" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="v_opr_pkb_id">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Jenis Pekerjaan</label>
                                            <select name="daily_trans_opr_pkb_id" id="daily_trans_opr_pkb_id" class="form-control form-control-sm" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Uraian</label>
                                            <input type="text" name="daily_trans_opr_pkb_title" id="daily_trans_opr_pkb_title" class="form-control form-control-sm" readonly placeholder="Uraian">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tanggal Aktivitas</label>
                                            <input type="text" name="daily_trans_opr_pkb_date" id="daily_trans_opr_pkb_date" class="form-control form-control-sm" readonly placeholder="Tgl. Awal s/d Tgl. Akhir">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapus" title="Hapus Data" onclick="doSimpan('hapus')">Hapus Data</button>
                    <button type="button" class="btn btn-secondary" id="btnBatal" title="Batal Transaksi" onclick="closeModal('modal_daily_trans')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" title="Simpan Data" value="" onclick="doSimpan(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_rkap_finance">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="modal-title no-margins" id="modal_rkap_title"></h4>
                        <button class="close" onclick="closeModal('modal_rkap_finance')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <button class="btn btn-primary" onclick="showModal('modal_create_rkap', '', 'add')">Tambah Data</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-sm table-bordered table-hover w-100" id="table_rkap_finance" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 7%;">No</th>
                                        <th class="text-left align-middle">Uraian</th>
                                        <th class="text-center align-middle" style="width: 10%;">Tahun</th>
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
    </div>

    <div class="modal fade" id="modal_create_rkap">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header div d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins modal-title" id='modal_create_rkap_title'></h4>
                    <button class="close" onclick="closeModal('modal_create_rkap')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-2 d-none">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>RKAP ID</label>
                                        <input type="text" class="form-control" id="rkap_id" name="rkap_id" placeholder="RKAP ID" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Uraian</label>
                                        <input type="text" class="form-control" id="rkap_title" name="rkap_title" placeholder="Uraian" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea name="rkap_description" id="rkap_description" class="form-control" rows="4" placeholder="Deskripsi"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Tahun</label>
                                        <input type="text" class="form-control" id="rkap_year" name="rkap_year" readonly placeholder="YYYY" style="background: white; cursor: pointer; height: 37.5px">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 border-left">
                            <table class="table table-sm table-hover" id="table_create_rkap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">&nbsp;</th>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Uraian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            
                            <label style="font-weight: normal;" class="text-danger">*) Tombol 'Enter' untuk menambah baris</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex flex-row align-items-center justify-content-end">
                        <button class="ml-2 btn btn-primary" value="1" onclick="tambahBaris('table_create_rkap', '')" title="Tambah Baris Table Detail" id="btnHapusBaris">Tambah Baris</button>
                        <button class="ml-2 btn btn-secondary" onclick="closeModal('modal_create_rkap')" title="Tutup Tampilan">Batal</button>
                        <button class="ml-2 btn btn-primary" title="Simpan Data" value="" onclick="doSimpanRKAP(this.value)" id="btnSimpanRKAP">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_absensi">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-center w-100">
                        <h4 class="no-margins">Download Absensi</h4>
                        <button class="close" onclick="closeModal('modal_absensi')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <label>Pilih Tanggal</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="text" class="form-control" style="height: 38px; background: white; cursor: pointer;" readonly placeholder="DD/MM/YYYY s/d DD/MM/YYYY" name="abs_tgl_cari" id="abs_tgl_cari">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" title="Download File Excel Absensi" id="btn_abs_download_excel" name="btn_abs_download_excel" onclick="downloadAbsen()">
                        <i class="fa fa-file-excel-o"></i> Download 
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_update_gapok_karyawan">
        <div class="modal-dialog scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="no-margins">Update Gaji Pokok Karyawan</h4>
                    <button class="close" onclick="closeModal('modal_update_gapok_karyawan')">&times;</button>
                </div>
                <div class="modal-body">
                    {{-- FOR BUTTON PURPOSE --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary">Tarik Data</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-bordered table-hover" style="width: 100%;" id="table_update_gapok_karyawan">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 8%;">No</th>
                                            <th class="text-center align-middle">Nama</th>
                                            <th class="text-center align-middle" style="width: 25%;">Divisi</th>
                                            <th class="text-center align-middle" style="width: 25%;">Gaji Pokok</th>
                                            <th class="text-center align-middle" style="width: 8%;">Aksi</th>
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

    <div class="modal fade" id="modal_simulasi_lemburan">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="no-margins">Simulasi Perhitungan Lembur</h4>
                    <button class="close" onclick="closeModal('modal_simulasi_lemburan')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="no-margins font-weight-bold">Pilih Karyawan</label>
                                <select name="sml_emp_id" id="sml_emp_id" style="width: 100%;"></select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="no-margins font-weight-bold">Pilih Tanggal</label>
                                <input type="text" class="form-control" name="sml_emp_date" id="sml_emp_date" readonly placeholder="DD/MM/YYYY s/s DD/MM/YYYY" style="background: white; cursor:pointer; height: 38px;">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="no-margins font-weight-bold text-white">Button</label><br>
                                <button type="button" class="btn btn-primary rounded-md" title="Cari Data" style="height: 38px;" onclick="doCari('modal_simulasi_lemburan')">Cari</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Nama</label>
                                        <input type="text" class="form-control form-control-sm" name="sml_emp_name" id="sml_emp_name" placeholder="Nama Karyawan" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Divisi</label>
                                        <input type="text" class="form-control form-control-sm" name="sml_emp_division" id="sml_emp_division" placeholder="Divisi" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Gaji Pokok</label>
                                        <h2 class="no-margins" name="sml_emp_fee" id="sml_emp_fee">Rp. 0.00</h2>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Pendapatan Per Jam <i class='fa fa-info-circle' style="color: blue; cursor: pointer;" title="Gaji Pokok / 173"></i></label>
                                        <h2 class="no-margins" name="sml_emp_fee_hourly" id="sml_emp_fee_hourly">Rp. 0.00</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Total Pendapatan Lembur</label>
                                        <h2 class="no-margins" name="sml_emp_fee_ovt" id="sml_emp_fee_ovt">Rp. 0.00</h2>
                                        <input type="hidden" id="sml_emp_fee_ovt_input">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Total OT1</label>
                                        <h3 class="no-margins" name="sml_emp_ot1" id="sml_emp_ot1">0</h3>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Total OT2</label>
                                        <h3 class="no-margins" name="sml_emp_ot2" id="sml_emp_ot2">0</h3>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Total OT3</label>
                                        <h3 class="no-margins" name="sml_emp_ot3" id="sml_emp_ot3">0</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h2 class="no-margins">Tabel Lemburan</h2><br>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-bordered" style="width: 100%;" id="table_emp_ovt">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%">No</th>
                                            <th class="text-center align-middle" style="width: 15%">Tanggal Lembur</th>
                                            <th class="text-center align-middle" style="width: 20%">OT 1 (16.00 s/d 17.00)</th>
                                            <th class="text-center align-middle" style="width: 20%">OT 2 (17.00 s/d 18.00)</th>
                                            <th class="text-center align-middle" style="width: 20%">OT 3 (18.00 s/d 01.00)</th>
                                            <th class="text-center align-middle" style="width: 8%">Pengajuan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2" class="text-right">Total :</th>
                                            <th class="text-right" id="table_emp_ovt_total_ot1">0</th>
                                            <th class="text-right" id="table_emp_ovt_total_ot2">0</th>
                                            <th class="text-right" id="table_emp_ovt_total_ot3">0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" title="Download File">
                        <i class="fa fa-download"></i> Download File
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/dashboard/index.js') }}"></script>
@endpush