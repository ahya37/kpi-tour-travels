@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
    label {
        font-weight: bold;
    }

    .menengah { 
        display     : flex;
        align-items : center;
        justify-content: center;
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
                        <h4 style="margin-top: 0px; margin-bottom: 0px;"><i class='fa fa-calendar'></i> &nbsp; Jadwal Umrah</h4>
                    </div>
                    <div class="card-body">
                        <h2 style="margin-bottom: 0px; margin-top: 0px;" class="text-right" id="dashboard_jadwal_umrah">0</h2>
                    </div>
                    <a href="{{ route('index.operasional.program') }}">
                        <div class="card-footer text-left">Lihat Detail</div>
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-success">
                        <h4 style="margin-top: 0px; margin-bottom: 0px;"><i class='fa fa-wrench'></i> &nbsp; Aturan Program Kerja</h4>
                    </div>
                    <div class="card-body">
                        <h2 style="margin-bottom: 0px; margin-top: 0px;" class="text-right" id="dashboard_rules">0</h2>
                    </div>
                    <a href="{{ route('index.operasional.rulesprokerbulanan') }}">
                        <div class="card-footer text-left">Lihat Detail</div>
                    </a>
                </div>
            </div>
            <div class="col-sm-3"></div>
            <div class="col-sm-3"></div>
        </div>
        <div class="row" style="padding-top: 24px;">
            <div class="col-sm-12">
                <div class="card bg-white">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3 class="card-title pt-1">
                                    <i class="fa fa-bar-chart"></i> Chart
                                </h3>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row d-flex">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-header bg-success">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h4 class="my-2">Chart Pekerjaan Tahun {{ date('Y') }}</h4>
                                            </div>
                                            {{-- <div class="col-sm-6 text-right">
                                                <div class="dropdown">
                                                    <a class="btn btn-success dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                                                        <i class='fa fa-filter'></i> Filter
                                                    </a>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#FilterChartAll">Semua</a>
                                                        <a class="dropdown-item" href="#FilterBulanan">Bulanan</a>
                                                        <a class="dropdown-item" href="#FilterChartHarian">Harian</a>
                                                    </div>
                                                    </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="menengah" style="height: 350px;" id="showLoading_chart">
                                            <div class="text-center">
                                                <div class="spinner-border" id="showLoading_chart_icon">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <br>
                                                <h4 id="showLoading_chart_text">Chart Sedang Dimuat</h4>
                                            </div>
                                        </div>
                                        <div class="wrapper" style="height: 350px; display: none;" id="showView_chart">
                                            <div class="text-center">
                                                <canvas id="myChart" height="350"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card my-auto">
                                    <div class="card-header bg-primary">
                                        <h4 class="my-2">List User Divisi Operasional</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="menengah" style="height: 350px;" id="showLoading_table">
                                            <div class="text-center">
                                                <div class="spinner-border" id="showLoading_table_icon">
                                                    <span class="sr-only">Loading...</span>
                                                </div>
                                                <br>
                                                <h4 id="showLoading_table_text">Table Sedang Dimuat</h4>
                                            </div>
                                        </div>
                                        <div class="wrapper" style="height: 350px; display: none;" id="showView_table">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover" id="table_ListUser">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center align-middle">No</th>
                                                            <th class="text-left align-middle">Nama</th>
                                                            <th class="text-left align-middle">Posisi</th>
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
                </div>
            </div>
        </div>
        <div class="row" style="padding-top: 24px;">
            <div class="col-sm-12">
                <div class="card bg-white">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3 class="card-title pt-1"><i class='fa fa-cog'></i> Generate Program Kerja Bulanan</h3>
                            </div>
                            <div class="col-sm-6 text-right pt-1">
                                <button type="button" class="btn btn-secondary" title='Filter Table' data-toggle='collapse' data-target='#filter'><i class='fa fa-filter'></i>&nbsp;Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="collapse" id="filter">
                                    <div class="card card-body">
                                        <div class="row">
                                            <div class="col-sm-2"><label>Bulan</label></div>
                                            <div class="col-sm-2"><label>Tahun</label></div>
                                            <div class="col-sm-3"><label>Paket</label></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-2"><select name="programFilterBulan" id="programFilterBulan" style="width: 100%;"></select></div>
                                            <div class="col-sm-2"><select name="programFilterTahun" id="programFilterTahun" style="width: 100%;"></select></div>
                                            <div class="col-sm-3"><select name="programFilterPaket" id="programFilterPaket" style="width: 100%;"></select></div>
                                            <div class="col-sm-2"><input type='button' id='programFilterBtnCari' class='btn btn-primary' value='Tampilkan' style='height: 37px;'></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="padding-top: 24px;">
                            <table class="table table-sm table-striped table-hover table-bordered" id="table_jadwal_umrah" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Paket</th>
                                        <th class="text-center">Pembimbing</th>
                                        <th class="text-center">Tgl. Keberangkatan</th>
                                        <th class="text-center">Tgl. Kepulangan</th>
                                        <th class="text-center">Aksi</th>
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

    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">List Program Kerja sudah tercapai</h4>
                    <button type="button" class="close" onclick="closeModal('modalForm')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-bordered" id="table_list_program_kerja">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Program Kerja</th>
                                            <th class="text-center align-middle">Tanggal</th>
                                            <th class="text-center align-middle">Bagian</th>
                                            <th class="text-center align-middle">Durasi</th>
                                            <th class="text-center align-middle">Tanggal Realisasi</th>
                                            <th class="text-center align-middle">Durasi Realisasi</th>
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
    
    <!-- Modal -->
    {{-- STATUS HOLD --}}
    <div class="modal fade" id="modaGenerateRules" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Generate Rules untuk Jadwal Umrah</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="card card-body">
                            <input type="hidden" class="form-control form-contorl-sm" id="jdw_id">
                            <div class="col-sm-6">
                                <div class="row mb-2">
                                    <span class="col-sm-4"><label>Program Umrah</label></span>
                                    <span class="col-sm-1">:</span>
                                    <span class="col-sm-7" id="programUmrah_text"></span>
                                </div>
                                <div class="row mb-2">
                                    <span class="col-sm-4"><label>Jadwal</label></span>
                                    <span class="col-sm-1">:</span>
                                    <span class="col-sm-7" id="programUmrah_Jadwal"></span>
                                </div>
                                <div class="row mb-2">
                                    <span class="col-sm-4"><label>Pembimbing</label></span>
                                    <span class="col-sm-1">:</span>
                                    <span class="col-sm-7" id="programUmrah_Pembimbing"></span>
                                </div>
                            </div>
                            <div class="col-sm-6"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-hover no-margins table-striped" style="width: 100%;" id="tableListRules">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle"><input type="checkbox" name="selectAll" id="selectAll" title='Pilih Semua' onclick='selectAllTable(`tableListRules`, this.id)'></th>
                                            <th class="text-left align-middle">Uraian</th>
                                            <th class="text-center align-middle">Durasi</th>
                                            <th class="text-center align-middle">SLA</th>
                                            <th class="text-center align-middle">Estimasi Tgl</th>
                                            <th class="text-center align-middle">PIC</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closeModal('modaGenerateRules')">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    {{-- MOMENT AREA --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script>
    {{-- NPROGRESS --}}
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    {{-- CHART JS --}}
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="{{ asset('js/divisi/operasional/index.operasional.js') }}"></script>
@endpush