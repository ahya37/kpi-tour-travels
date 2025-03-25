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

    input[type="text"] {
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
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row mb-3">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <h5>Karyawan</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="emp_total" title="Total Karyawan">
                                <i class="fa fa-spinner fa-spin"></i>
                            </label>
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
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <h5>Pengajuan Cuti</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="pgj_total" title="Total Pengajuan">
                                <i class="fa fa-spinner fa-spin"></i>
                            </label>
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
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="ibox w-100">
                    <div class="ibox-title">
                        <div class="ibox-tools">
                            <span class="label label-primary float-right" id="abs_curr_date">@php echo date('Y-m-d') @endphp</span>
                        </div>
                        <h5 class="no-margins">Absensi</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="abs_total" title="Total Absensi Hari Ini">
                                <i class="fa fa-spinner fa-spin"></i>
                            </label>
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
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="ibox w-100">
                    <div class="ibox-title" style="padding-right:0px;">
                        <h5>Pengajuan Lembur</h5>
                    </div>
                    <div class="ibox-content">
                        <h1 class="no-margins">
                            <label id="pgj_lmb_total" title="Total Pengajuan Lembur">
                                <i class="fa fa-spinner fa-spin"></i>
                            </label>
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

    @include('divisi.human_resource.dashboard.modal_pengajuan_cuti')
    
    @include('divisi.human_resource.dashboard.modal_karyawan')

    @include('divisi.human_resource.dashboard.modal_absensi_karyawan')

    @include('divisi.human_resource.dashboard.modal_pengajuan_lembur')
    
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/human_resource/dashboard/index.hr.dashboard.js') }}"></script>
@endpush