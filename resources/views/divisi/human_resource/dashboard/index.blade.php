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
                        <a href="#">Lihat Detail</a>
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
                        <a href="#">Lihat Detail</a>
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
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/human_resource/dashboard/index.hr.dashboard.js') }}"></script>
@endpush