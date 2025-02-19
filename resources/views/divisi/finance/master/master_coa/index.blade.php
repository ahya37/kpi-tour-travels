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

        input[type=text] {
            height: 37.5px;
        }

        .text-primary {
            color: #1ab394;
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
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h4 class="no-margins card-title">Tabel COA</h4>
                        <button class="btn btn-sm btn-primary" title="Tambah Data" onclick="showModal('modal_form_coa', 'add', '')"><i class="fa fa-plus"></i> Tambah COA</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <h4 class="font-weight-bold">Filter</h4>
                            </div>
                            <div class="col-xl-3 col-md-4 col-12">
                                <select name="filter_coa_kode" id="filter_coa_kode" title="Filter COA" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, this.value)"></select>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="table_list_coa">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle" style="width: 5%;">No</th>
                                                <th class="text-center align-middle" style="width: 15%;">Kode</th>
                                                <th class="text-left align-middle">Deskripsi</th>
                                                <th class="text-center align-middle" style="width: 8%;">Aksi</th>
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

    <div class="modal fade" id="modal_form_coa">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex-flex-row align-items-center justify-content-between">
                    <h4 class="no-margins">Form <span id="form_coa_type"></span> COA (Chart of Accounts)</h4>
                    <button class="close" onclick="closeModal('modal_form_coa')" title="Tutup Form">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="coa_parent">Referensi Kode</label>
                                <select name="coa_parent" id="coa_parent" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                            </div>
                        </div>
                        <form method="POST" id="form_coa">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="coa_id">Kode CoA</label>
                                    <input type="hidden" class="form-control" id="coa_id_level" name="coa_id_level" readonly>
                                    <div class="row">
                                        <div class="col-5">
                                            <input type="text" class="form-control" id="coa_id_parent" name="coa_id_parent" readonly placeholder="Referensi Kode">
                                        </div>
                                        <div class="col-7">
                                            <input type="text" class="form-control" id="coa_id_child" name="coa_id_child" placeholder="Kode CoA">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="coa_description">Deskripsi</label>
                                    <textarea name="coa_description" id="coa_description" rows="4" style="resize: none;" placeholder="Deskripsi.." class="form-control"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" title="Tutup Tampilan" id="btnBatalFormCoa" onclick="closeModal('modal_form_coa')">Batal</button>
                    <button class="btn btn-primary" title="Simpan" value="" id="btnSimpanFormCoa" onclick="simpanData('form_coa', this.value, '')">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/master/coa/coa.index.js') }}"></script>
@endpush