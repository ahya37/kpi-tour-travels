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
        height: 38px;
    }

    input[type=text]:read-only.form-control {
        cursor: pointer;
    }

    input[type=text].form-control {
        height: 38px;
    }

    input[type=email].form-control {
        height: 38px;
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
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="no-margins">Summary Data Umhaj Tahun @php echo date('Y') @endphp</h1>
                </div>
            </div>
            <div class="row align-items-center">
                <div class="col-xl-3 col-lg-6 mb-xl-0 mb-4">
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
                <div class="col-xl-3 col-lg-6 mb-xl-0 mb-4">
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
                <div class="col-xl-3 col-lg-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins">Agen</h4>
                        </div>
                        <div class="card-body text-right" id="dashboard_agent_total_data">
                            <div class="spinner-border"></div>
                        </div>
                        <a href="#show_daftar_agent" class="card-footer" onclick="showModal('modal_agent', '')">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins">Jemaah</h4>
                        </div>
                        <div class="card-body text-right" id="dashboard_member_total_data">
                            <div class="spinner-border"></div>
                        </div>
                        <a href="#show_daftar_member" class="card-footer" onclick="showModal('modal_member_all', '')">
                            Lihat Detail
                        </a>
                    </div>
                </div>
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
                                <h2 class="no-margins">Jumlah Jemaah</h2>
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
                    <div class="row align-items-top">
                        <div class="col-sm-6">
                            <div class="row ml-2 align-items-center">
                                <div class="col-sm-4">
                                    <label class="no-margins font-weight-bold">
                                        <h4 class="no-margins">Tour Code</h4>
                                    </label>
                                </div>
                                <div class="col-sm-8">
                                    <label class="no-margins font-weight-normal">
                                        <h4 class="font-weight-normal no-margins" id="umrah_list_detail_tour_code">{tour_code}</h4>
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
                                        <h4 class="font-weight-normal no-margins" id="umrah_list_detail_date">{icon_plane_depature} {depature_date} & {icon_plane_arrival} {arrival_date}</h4>
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
                                        <h4 class="font-weight-normal no-margins" id="umrah_list_detail_mentor">{icon_user} {mentor_name}</h4>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
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
    
    <div class="modal fade" id="modal_agent">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="modal-title no-margins">
                        <label class="no-margins">List Agent</label>
                    </h4>
                    <button class="close" titl="Tutup Tampilan" onclick="closeModal('modal_agent')">&times;</button>
                </div>
                <div class="modal-body">
                    {{-- TABLE --}}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-borderd table-hovered" id="table_list_agent" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Nama</th>
                                            <th class="text-center align-middle">PIC</th>
                                            <th class="text-center align-middle">Kontak</th>
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
    </div>

    <div class="modal fade" id="modal_member_all">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header w-100 d-flex flex-row align-items-center justify-content-between">
                    <h4 class="modal-title no-margins font-weight-bold">List Member / Jemaah</h4>
                    <button type="button" class="close" title="Tutup Tampilan" onclick="closeModal('modal_member_all')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-primary" onclick="showModal('modal_form_member', '')" disabled>Tambah Data</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table-sm table table-striped table-table-bordered table-hover" id="tableModalMemberAll" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Nama</th>
                                            <th class="text-center align-middle">Kota</th>
                                            <th class="text-center align-middle">Alamat</th>
                                            <th class="text-center align-middle">Aksi</th>
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
    </div>

    <div class="modal fade" id="modal_form_member">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                    <h4 class="no-margins font-weight-bold">Form <span id="modal_form_member_jenis">&nbsp;</span> Data Member</h4>
                    <button class="close" onclick="closeModal('modal_form_member')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-12">
                            <h4 class="font-weight-normal no-margins">
                                Note : Tanda (<span class="text-danger">*</span>) = Harus Diisi
                            </h4>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="member_tab">
                                <li class="nav-item">
                                    <a href="#" class="nav-link active" data-toggle="tab" data-target="#tab_data_pribadi" role="tab" id="nav_data_pribadi">Data Pribadi</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" data-toggle="tab" data-target="#tab_data_passport" role="tab" id="nav_data_passport">Data Passport</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" data-toggle="tab" data-target="#tab_data_alamat" role="tab" id="nav_data_alamat">Data Alamat</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" data-toggle="tab" data-target="#tab_data_pekerjaan" id="nav_data_pekerjaan">Data Pekerjaan</a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link" data-toggle="tab" data-target="#tab_data_keterangan" id="nav_data_keterangan">Catatan</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content p-3 border-right border-left border-bottom rounded-bottom">
                        <div class="tab-pane fade" id="tab_data_pribadi">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h2 class="font-weight-light no-margins">Data Pribadi</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Depan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mb_nm_dp" name="mb_nm_dp" placeholder="Nama Depan">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Tengah</label>
                                        <input type="text" class="form-control" id="mb_nm_tg" name="mb_nm_tg" placeholder="Nama Tengah">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Belakang</label>
                                        <input type="text" class="form-control" id="mb_nm_bl" name="mb_nm_bl" placeholder="Nama Belakang">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="mb_nm" name="mb_nm" placeholder="Nama" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Ayah</label>
                                        <input type="text" class="form-control" id="mb_nm_ay" name="mb_nm_ay" placeholder="Nama Ayah">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">NIK <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="mb_nik" name="mb_nik" placeholder="NIK" min="0" step="1">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="mb_tmp_lh" name="mb_tmp_lh" placeholder="Tempat Lahir">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Tanggal Lahir</label>
                                        <input type="text" class="form-control" id="mb_tgl_lh" name="mb_tgl_lh" placeholder="DD/MM/YYYY" readonly>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Usia</label>
                                        <h3 class="font-weight-bold no-margins text-primary w-100">
                                            <label class="no-margins d-none" id="mb_tgl_ag"></label>
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Jenis Kelamin <span class="text-danger">*</span></label>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_jk" id="mb_jk_1" value="L">
                                            <label class="form-check-label font-weight-normal" for="mb_jk_1" style="cursor: pointer;">Laki-Laki</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_jk" id="mb_jk_2" value="P">
                                            <label class="form-check-label font-weight-normal" for="mb_jk_2" style="cursor: pointer;">Perempuan</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Status</label>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_st" id="mb_st_1" value="Belum Menikah">
                                            <label for="mb_st_1" class="form-check-label font-weight-normal" style="cursor: pointer;">Belum Menikah</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_st" id="mb_st_2" value="Sudah Menikah">
                                            <label for="mb_st_2" class="form-check-label font-weight-normal" style="cursor: pointer;">Sudah Menikah</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_st" id="mb_st_3" value="Janda">
                                            <label for="mb_st_3" class="form-check-label font-weight-normal" style="cursor: pointer;">Janda</label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" class="form-check-input" name="mb_st" id="mb_st_4" value="Duda">
                                            <label for="mb_st_4" class="form-check-label font-weight-normal" style="cursor: pointer;">Duda</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Mahram</label>
                                        <select class="form-control" name="mb_nm_mhr" id="mb_nm_mhr" style="width: 100%;" data-placeholder="Mahram"></select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Status Mahram</label>
                                        <select class="form-control" name="mb_st_mhr" id="mb_st_mhr" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_data_passport">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h2 class="font-weight-light no-margins">Data Passport</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">No. Passport</label>
                                        <input type="text" class="form-control" id="mb_nm_psp" name="mb_nm_psp" placeholder="No. Passport">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Tgl. Terbit</label>
                                        <input type="text" class="form-control" id="mb_tgl_psp" name="mb_tgl_psp" placeholder="DD/MM/YYYY" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Tempat Terbit</label>
                                        <input type="text" class="form-control" id="mb_tmp_psp" name="mb_tmp_psp" placeholder="Tempat Terbit Passport">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Tgl. Kadaluarsa</label>
                                        <input type="text" class="form-control" id="mb_tgl_psp_exp" name="mb_tgl_psp_exp" placeholder="DD/MM/YYYY" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">NPWP</label>
                                        <input type="text" class="form-control" id="mb_npwp_num" name="mb_npwp_num" placeholder="NPWP">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama NPWP</label>
                                        <input type="text" class="form-control" id="mb_npwp_name" name="mb_npwp_name" placeholder="Nama Pemilik NPWP">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Status Pemilik NPWP</label>
                                        <select name="mb_npwp_stat" id="mb_npwp_stat" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_data_alamat">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h2 class="font-weight-light no-margins">Data Alamat</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Alamat</label>
                                        <textarea name="mb_adr" id="mb_adr" rows="4" style="resize:none;" placeholder="Tulis Alamat.." class="form-control" onkeyup="generateAddressLetter()"></textarea>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Alamat Surat</label>
                                        <textarea name="mb_adr_ltr" id="mb_adr_ltr" rows="4" style="resize: none;" placeholder="Automatic Generate" class="form-control" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Provinsi</label>
                                        <select name="mb_pl_prv" id="mb_pl_prv" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, '', this.value), generateAddressLetter()"></select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Kota / Kabupaten</label>
                                        <select name="mb_pl_ct" id="mb_pl_ct" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, '', this.value), generateAddressLetter()"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Kecamatan</label>
                                        <select name="mb_pl_kc" id="mb_pl_kc" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, '', this.value), generateAddressLetter()"></select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Kelurahan</label>
                                        <select name="mb_pl_kl" id="mb_pl_kl" class="form-control" style="width: 100%;" onchange="generateAddressLetter()"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">RT</label>
                                        <input type="number" class="form-control" placeholder="No. RT" step="1" min="0" max="999" id="mb_pl_rt" onkeyup="generateAddressLetter()">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">RW</label>
                                        <input type="number" class="form-control" placeholder="No. RW" step="1" min="0" max="999" id="mb_pl_rw" onkeyup="generateAddressLetter()">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Kode Pos</label>
                                        <input type="number" class="form-control" placeholder="Kode Pos" step="1" min="0" max="99999" id="mb_pl_pc" onkeyup="generateAddressLetter()">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Telepon</label>
                                        <input type="text" class="form-control" placeholder="No. Telepon" id="mb_ct_tlp" name="mb_ct_tlp">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Handphone</label>
                                        <input type="text" class="form-control" placeholder="No. Handphone" id="mb_ct_hp" name="mb_ct_hp">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Alamat Email</label>
                                        <input type="email" class="form-control" placeholder="Alamat Email" id="mb_ct_em" name="mb_ct_em">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Ukuran Baju</label>
                                        <select name="mb_sz_bd" id="mb_sz_bd" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_data_pekerjaan">
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h2 class="font-weight-light no-margins">Data Pekerjaan</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Pekerjaan</label>
                                        <select name="mb_job" id="mb_job" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Perusahaan</label>
                                        <input type="text" class="form-control" id="mb_cp_name" name="mb_cp_name" placeholder="Nama Perusahaan">
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Pendidikan</label>
                                        <select name="mb_acd" id="mb_acd" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-8">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Sumber Informasi <span class="text-danger">*</span></label>
                                        <select name="mb_sc" id="mb_sc" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, '', this.value)"></select>
                                    </div>
                                </div>
                                <div class="col-4" id="mb_agt_form">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Nama Agen <span class="text-danger">*</span></label>
                                        <select name="mb_agt_name" id="mb_agt_name" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Dilayani Oleh <span class="text-danger">*</span></label>
                                        <select name="mb_cs_name" id="mb_cs_name" style="width: 100%;" class="form-control"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="tab_data_keterangan">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="font-weight-normal no-margins">Catatan</label>
                                        <textarea name="mb_dsc" id="mb_dsc" rows="4" class="form-control" placeholder="Tulis Keterangan"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="modal_form_member_btnClose" title="Tutup Tampilan" onclick="closeModal('modal_form_member')">Tutup</button>
                    <button class="btn btn-primary" id="modal_form_member_btnSimpan" title="Simpan Data" value="add" onclick="doSimpan('member', this.value)">Simpan</button>
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