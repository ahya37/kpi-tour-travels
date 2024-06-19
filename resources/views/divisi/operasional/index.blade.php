@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

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
                                            <div class="col-sm-3"><select name="programFilterPaket" id="programFlterPaket" style="width: 100%;"></select></div>
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

    <div class="modal fade" id="modalForm" >
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
                                            <th>No</th>
                                            <th>Program Kerja</th>
                                            <th>Tanggal</th>
                                            <th>Bagian</th>
                                            <th>Tanggal Realisasi</th>
                                            <th>Durasi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="modal-footer">
                    <button type="button" class="btn btn-secondary">Tutup</button>
                    <button type="button" class="btn btn-primary">Save</button>
                </div> --}}
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
    <script src="{{ asset('js/divisi/operasional/index.operasional.js') }}"></script>
@endpush