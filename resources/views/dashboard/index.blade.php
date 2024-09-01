@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')

    {{-- DATATABLES --}}
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/fixedHeader-3.2.0/fixedHeader.dataTables.min.css') }}">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" href="{{ asset('css/customCSS/daterangepicker/daterangepicker.css') }}">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endpush

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $sub_title ?? '' }}</h2>
        </div>
    </div>
@endsection

@section('content')
    {{-- <div class="wrapper wrapper-content animated fadeInRight border my-4" style="height: 50vh;">
        <div class="row">

        </div>
    </div> --}}
    <div class="container-fluid">
        <div class="wrapper wrapper-content">
            <input type="hidden" id="prs_user_id" value="{{ $user_id }}">
            {{-- TABLE ABSEN --}}
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">
                            <label class="no-margins">Table List Absensi User</label>
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <label class="font-weight-bold">Tanggal Cari</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <input type="text" name="prs_tgl_cari" id="prs_tgl_cari" class="form-control" style="height: 38px; background: white; cursor: pointer;" readonly placeholder="DD/MM/YYYY s/d DD/MM/YYYY" title="Masukkan Tanggal Awal dan Tanggal Akhir">
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-primary" id="btn_cari_data_absen" title="Cari Data Absen" style="height: 38px;">Cari</button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-striped table-hover table-bordered table-striped" style="width: 100%;" id="table_absensi">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" title="Tanggal Absensi">Tanggal</th>
                                        <th class="text-center align-middle" title="Jam Datang">Jam Masuk</th>
                                        <th class="text-center align-middle" title="Jam Pulang">Jam Keluar</th>
                                        <th class="text-center align-middle" title="Total Jam Keterlambatan">Kurang Jam</th>
                                        <th class="text-center align-middle" title="Total Jam Overtime">Lebih Jam</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-right">Total Jam : </th>
                                        <th id="table_absensi_total_kurang_jam">00:00:00</th>
                                        <th id="table_absensi_total_lebih_jam">00:00:00</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row">
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="no-margins" id="prs_date">Absensi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <h1>
                                        <label id="prs_time">00:00:00</label>
                                    </h1>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-primary w-100" onclick="showModal('modalShowCamera', 'masuk')">
                                        <i class="fa fa-sign-in"></i> Absen Masuk
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-danger w-100" onclick="showModal('modalShowCamera', 'keluar')">
                                        <i class="fa fa-sign-out"></i> Absen Pulang
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="d-flex flex-column w-100">
                                        <span id="prs_text_masuk"></span>
                                        <span id="prs_text_keluar"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <small class="text-danger">* Absensi Masih Dalam Tahap Pengembangan</small>
                </div>
            </div> --}}
            {{-- <button onclick="getLocation()">Show Location</button>
            <div id="location"></div> --}}
        </div>     
    </div>
@endsection

@push('addon-script')
    {{-- MOMENT --}}
    <script src="{{ asset('js/customJS/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/id.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/moment-timezone-with-data.min.js') }}"></script>
    {{-- DATATABLE --}}
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/buttons.dataTables.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/fixedHeader-3.2.0/dataTables.fixedHeader.min.js') }}"></script>
    {{-- SWEETALERT2 --}}
    <script src="{{ asset('js/customJS/SweetAlert/sweetalert2.all.min.js') }}"></script>
    {{-- DATERANGEPICKER --}}
    <script src="{{ asset('js/customJS/daterangepicker/daterangepicker.min.js') }}"></script>

    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/dashboard/index.dashboard.js') }}"></script>
@endpush
