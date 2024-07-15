@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link rel="stylesheet" href="{{ asset('assets/css/swal2.custom.css') }}">
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
                <div class="card shadow">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="card-title py-2" style="margin: 0px;">List Table Program Marketing</h4>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button class="btn btn-primary" data-toggle="modal" onclick="show_modal('modalForm', 'add', '')">
                                    <i class="fa fa-plus"></i> Tambah Data
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-borderd table-hover" id="table_program">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Uraian</th>
                                                <th>Aksi</th>
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
    <!-- Modal -->
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title py-2" style="margin: 0px;">Tambah Data Sasaran</h4>
                    <button type="button" class="close pt-2 pr-2" data-dismiss="modal" aria-label="Close" onclick="close_modal('modalForm')" style="margin: 0px; padding: 0px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formProkerAdd">
                        <div class="row">
                            <div class="col-sm-6 border-right">
                                <div class="form-row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="prokTahunanTitle"><b>Judul</b></label>
                                            <input type="hidden" class="form-control form-control-sm" id="prokTahunanID" name="prokTahunanID" placeholder="ID Program Kerja">
                                            <input type="text" class="form-control form-control-sm" id="prokTahunanTitle" name="prokTahunanTitle" placeholder="Judul Program Kerja" autocomplete="off" style="height: 37.5px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="prokTahunanDesc"><b>Deskripsi</b></label>
                                            <textarea name="prokTahunanDesc" id="prokTahunanDesc" class="form-control form-control-sm" rows="4" placeholder="Tulis Deskripsi"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="prokTahunanTime"><b>Masa Kerja</b></label>
                                            <input type="text" id="prokTahunanTime" name="prokTahunanTime" class="form-control form-control-sm date-picker-year" placeholder="YYYY" readonly style="cursor: pointer; background: white; height: 37.5px;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="prokTahunanList"><b>Detail Program Kerja</b></label>
                                            <table class="table table-sm table-bordered" id="tblSubProk" style="width: 100%; margin-top: -6px;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center align-middle">&nbsp;</th>
                                                        <th class="text-center align-middle">No</th>
                                                        <th class="text-center align-middle">Judul</th>
                                                        <th class="text-center align-middle">Target</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 text-right">
                                                <button type="button" class="btn btn-sm btn-primary" id="btnTambahBarisSubProk" value="1" onclick="tambahBaris('tblSubProk','')">
                                                <i class="fa fa-plus"></i> Tambah Data
                                            </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancel" data-dismiss="modal" onclick="close_modal('modalForm')">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnSave" onclick="do_simpan(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
@endpush