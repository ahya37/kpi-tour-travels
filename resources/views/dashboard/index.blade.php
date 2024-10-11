@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')

    {{-- DATATABLES --}}
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/fixedHeader-3.2.0/fixedHeader.dataTables.min.css') }}">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" href="{{ asset('css/customCSS/daterangepicker/daterangepicker.css') }}">
    {{-- SELECT2 --}}
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">

    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    <style>
        label {
            margin: 0;
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
    {{-- <div class="wrapper wrapper-content animated fadeInRight border my-4" style="height: 50vh;">
        <div class="row">

        </div>
    </div> --}}
    <div class="container-fluid">
        <div class="wrapper wrapper-content">
            <input type="hidden" id="prs_user_id" value="{{ $user_id }}">
            <div class="row mb-3 text-center">
                <div class="col-sm-12">
                    <h2 class="font-weight-light no-margins">Kehadiran Bulan <b>@php echo date('F'); @endphp</b></h2>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins font-weight-bold card-title">Kehadiran</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="font-weight-bold no-margins" id="dashboard_total_absen_text">0</h1>
                        </div>
                        <a href="#show_attendance">
                            <div class="card-footer" onclick="showModal('modal_total_absen', '')">Lihat Detail</div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins font-weight-bold card-title">Izin / Sakit / Cuti</h4>
                        </div>
                        <div class="card-body">
                            <h1 class="font-weight-bold no-margins" id="dashboard_total_isc_text">0</h1>
                        </div>
                        <a href="#show_no_attendance">
                            <div class="card-footer" onclick="showModal('modal_total_ketidakhadiran', '')">Lihat Detail</div>
                        </a>
                    </div>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div> 
    </div>
    <div class="modal fade" id="modal_total_absen">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="modal-title" class="no-margins font-weight-bold"><label>List Absensi</label></h4>
                    <button class="close" onclick="closeModal('modal_total_absen')" title="Tutup Tampilan">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <h2 class="no-margins">{{ $user_name }}</h2>
                            <div class="row mt-2">
                                <div class="col-sm-5">
                                    <label class="font-weight-bold">Total Kehadiran</label>
                                </div>
                                <div class="col-sm-6">
                                    <label id="tbl_total_absensi">0</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-5">
                                    <label class="font-weight-bold">Total Jam Keterlambatan</label>
                                </div>
                                <div class="col-sm-6">
                                    <label id="tbl_total_absen_keterlambatan_1">00:00:00</label>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-5">
                                    <label class="font-weight-bold">Total Jam Lebih</label>
                                </div>
                                <div class="col-sm-6">
                                    <label id="tbl_total_absen_lebih_jam_1">00:00:00</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <h2 class="no-margins">Filter</h2>
                            <div class="row mt-2">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Pilih Bulan</label>
                                        <select class="form-control" name="tbl_filter_month" id="tbl_filter_month" style="width: 100%;"></select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="text-white">test</label><br>
                                        <button class="btn btn-primary" title="Cari Data" id="tbl_filter_button" onclick="cariData('tbl_total_absen')" style="height: 38px;">Cari Data</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover" style="width: 100%;" id="tbl_total_absen">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal Absen</th>
                                            <th class="text-center align-middle">Jam Masuk</th>
                                            <th class="text-center align-middle">Jam Pulang</th>
                                            <th class="text-center align-middle">Jam Kerja</th>
                                            <th class="text-center align-middle">Keterlambatan</th>
                                            <th class="text-center align-middle">Lebih Jam</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" id="tbl_total_absen_title">Total Jam : </th>
                                            <th id="tbl_total_absen_jam_kerja">&nbsp;</th>
                                            <th id="tbl_total_absen_keterlambatan">00:00:00</th>
                                            <th id="tbl_total_absen_lebih_jam">00:00:00</th>
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
    <div class="modal fade" id="modal_total_ketidakhadiran">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header align-items-center justify-content-between">
                    <h4 class="modal-title no-margins">
                        <label class="no-margins font-weight-bold">List Izin / Sakit / Cuti</label>
                    </h4>
                    <button class="close" onclick="closeModal('modal_total_ketidakhadiran')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2 class="no-margins font-weight-thin">
                                <label class="no-margins">Filter</label>
                            </h2>
                            <br>
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold">Pilih Bulan</label>
                                        <select name="filter_bulan_cuti" id="filter_bulan_cuti" style="width: 100%;"></select>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="no-margins font-weight-bold text-white" style="width: 100%;">test</label>
                                        <button class="btn btn-primary" id="btn_filter_bulan_cuti" style="height: 38px;" title="Filter" onclick="cariData('tbl_total_cuti')">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="tbl_total_cuti">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Tanggal Pengajuan</th>
                                            <th class="text-center align-middle">Keterangan</th>
                                            <th class="text-center align-middle">Jenis Pengajuan</th>
                                            <th class="text-center align-middle">Status Pengajuan</th>
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
    {{-- SELECT2 --}}
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>

    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/dashboard/index.dashboard.js') }}"></script>
@endpush
