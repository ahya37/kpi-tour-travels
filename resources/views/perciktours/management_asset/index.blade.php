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
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <div class="row align-items-center">
                            <div class="col-xl-6 col-12 text-xl-left text-center">
                                <h4 class="no-margins">
                                    <label class="font-weight-bold no-margins">Manajemen Asset Umrah</label>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hovered table-bordered" id="table_management_asset_umrah" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tour Code</th>
                                        <th class="text-center align-middle">Tgl. Keberangkatan</th>
                                        <th class="text-center align-middle">Pembimbing</th>
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

    <div class="modal fade" id="modal_form_asset_umrah">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins">
                        <label class="no-margins font-weight-bold">Asset Untuk <span id="tour_code_title"></span></label>
                    </h4>
                    <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_form_asset_umrah')">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="ast_form">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="ast_tour_code">Tour Code</label>
                                    <input type="text" class="form-control" id="ast_tour_code" placeholder="Tour Code" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="ast_depature_date">Tgl. Keberangkatan</label>
                                    <input type="text" class="form-control" id="ast_depature_date" placeholder="Tgl. Keberangkatan" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="ast_arrival_date">Tgl. Kepulangan</label>
                                    <input type="text" class="form-control" id="ast_arrival_date" placeholder="Tgl. Kepulangan" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="ast_mentor_name">Pembimbing</label>
                                    <input type="text" class="form-control" id="ast_mentor_name" placeholder="Nama Pembimbing" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hovered table-bordered" id="table_form_asset_umrah">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>No</th>
                                            <th>Jenis</th>
                                            <th>URL</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" id="btn_tambah_baris_form_asset" value="1" onclick="addRowTable('table_form_asset_umrah', this.value, '')"><i class="fa fa-plus"></i> Tambah Baris</button> |
                    <button class="btn btn-primary" id="btn_simpan_form_asset" onclick="doSimpan('asset_umrah', this.value)"><i class="fa fa-save"></i> Simpan Data</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/perciktours/management_asset/index.js') }}"></script>
@endpush