@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://unpkg.com/nprogress@0.2.0/nprogress.css" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
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
            <div class="col-lg-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-sm-12">
                                <h1 style="margin-top: 10px;">Program Kerja Tahun - @php echo date('Y') @endphp</h1>
                            </div>
                        </div>
                        <hr>
                        <div class="row" id="summary_header" style="display: none;">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <b>Program Kerja Tahunan</b>
                                    </div>
                                    <div class="card-body text-right">
                                        <h2 id="pk_tahunan">0 Program Kerja</h2>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ Route('programKerja.tahunan.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <b>Program Kerja Bulanan</b>
                                    </div>
                                    <div class="card-body text-right">
                                        <h2 id="pk_bulanan">0 Program Kerja</h2>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ Route('programKerja.bulanan.index') }}">Lihat Detail</a>
                                    </div>
                                </div>    
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <b>Program Kerja Harian</b>
                                    </div>
                                    <div class="card-body text-right">
                                        <h2 id="pk_harian">0 Program Kerja</h2>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ Route('programKerja.harian.index') }}">Lihat Detail</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12" id="v_table_programKerja_bulanan" style="display: none;">
                <div class="card shadow my-4">
                    <div class="card-header bg-secondary text-white">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 style="padding: 0px;" class="pt-2"><i class="fa fa-table"></i> &nbsp; Table List Program Kerja Bulanan</h4>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button class="btn btn-secondary font-bold" data-toggle="collapse" data-target="#filter">
                                    <h4> <i class="fa fa-filter"></i> Filter</h4>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="collapse" id="filter">
                                    <div class="card card-body">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label>Bulan</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Divisi</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Dibuat Oleh</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <select ></select>
                                            </div>
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-4"></div>
                                            <div class="col-sm-4"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-4">
                                <div class="table-responsive">
                                    <table class="table-sm table-hover table-bordered" style="width: 100%;" id="table_programKerja_bulanan">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="text-left align-middle">Uraian</th>
                                                <th class="text-left align-middle">Tanggal</th>
                                                <th class="text-center align-middle">Divisi</th>
                                                <th class="text-center align-middle">Dibuat Oleh</th>
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
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')

    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/programKerja/dashboard/index.js') }}"></script>
@endpush