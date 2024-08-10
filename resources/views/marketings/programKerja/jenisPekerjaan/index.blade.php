@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link rel="stylesheet" href="{{ asset('assets/css/swal2.custom.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/yearpicker/yearpicker.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">

    <style>
    label {
        font-weight: bold;
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
    <input type="hidden" id="current_role" value="{{ $current_role }}">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <h4 class="no-margins">
                                Kalendar Tahun <span id="jpk_year_periode"><i class="fa fa-spinner fa-spin"></i></span> Bulan <span id="jpk_month_periode"><i class="fa fa-spinner fa-spin"></i></span>
                            </h4>
                            <button class="btn btn-primary" onclick="show_modal('modal_list_pekerjaan')">
                                <span class="badge badge-white"><i class='fa fa-spinner fa-spin'></i></span>
                                List Pekerjaan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="calendar" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTransJenisPekerjaan">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalJenisPekerjaan_title">Tambah Data Jenis Pekerjaan</h4>
                    <button class="close" onclick="close_modal('modalTransJenisPekerjaan')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan ID</label>
                                <input type="text" class="form-control form-control-sm" id="jpk_ID" name="jpk_ID" placeholder="ID" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program</label>
                                <select name="jpk_programID" id="jpk_programID" style="width: 100%;" class="form-control form-control-sm" onchange="show_select('jpk_programDetail', this.value, '', true)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <select name="jpk_programDetail" id="jpk_programDetail" style="width: 100%;" class="form-control form-control-sm"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Awal Aktivitas</label>
                                <input type="hidden" name="jpk_date" id="jpk_date">
                                <input type="text" class="form-control form-control-sm waktu" id="jpk_start_time" name="jpk_start_time" readonly style="background: white; cursor: pointer; height: 37.5px;">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Akhir Aktivitas</label>
                                <input type="text" class="form-control form-control-sm waktu" id="jpk_end_time" name="jpk_end_time" readonly style="background: white; cursor: pointer; height: 37.5px;">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <input type="text" class="form-control form-control-sm" id="jpk_title" name="jpk_title" placeholder="Uraian" autocomplete="off" style="height: 37.5px;">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Banyaknya Aktivitas</label>
                                <input type="text" class="form-control" id="jpk_aktivitas" name="jpk_aktivitas" placeholder="Banyaknya Aktivitas" inputmode="numeric" style="height: 37.5px;" value="1">
                                <small style="color: #dc3545">Note : Diisi dengan banyaknya aktivitas yang akan dikerjakan</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <textarea name="jpk_description" id="jpk_description" placeholder="Tulis Deskripsi Pekerjaan" rows="4" class="form-control form-control-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" id="jpk_btnHapus" onclick="hapus_data('modalTransJenisPekerjaan')" disabled>Hapus Data</button>
                    <button class="btn btn-secondary" onclick="close_modal('modalTransJenisPekerjaan')">Batal</button>
                    <button class="btn btn-primary" id="jpk_btnSimpan" onclick="do_simpan('modalTransJenisPekerjaan', this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_list_pekerjaan">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row justify-content-between align-items-center w-100">
                        <h4 class="no-margins">List Pekerjaan Bulan <span id="month_modal_list_pekerjaan"></span></h4>
                        <button class="close" onclick="close_modal('modal_list_pekerjaan')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-sm table-striped table-hover" id="table_list_pekerjaan" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 2%;">No</th>
                                        <th class="text-center align-middle" style="width: 35%;">Program</th>
                                        <th class="text-center align-middle">Uraian</th>
                                        <th class="text-center align-middle" style="width: 15%;">Target</th>
                                        <th class="text-center align-middle" style="width: 15%">Tipe</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" id="target_table_list_pekerjaan">Total : </th>
                                        <th id="total_table_list_pekerjaan"></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/programKerja/jenisPekerjaan/index.js') }}"></script>
@endpush