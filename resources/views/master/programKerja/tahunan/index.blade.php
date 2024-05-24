@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
    .dataTables_wrapper {
        padding-bottom: 0px;
        margin-top: -6px;
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
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <button type="button" class="btn btn-primary" onclick="show_modal('modalTambahDataProkerTahunan','', 'tambah_data')">Tambah Data</button>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm table-bordered table-hover dataTable" id="tableProgramKerjaTahunan">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">No</th>
                                            <th class="text-center" style="vertical-align: middle;">Judul Program Kerja</th>
                                            <th class="text-center" style="vertical-align: middle;">Masa Program Kerja</th>
                                            <th class="text-center" style="vertical-align: middle;">Total Program</th>
                                            <th class="text-center" style="vertical-align: middle;">Aksi</th>
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
    
    <!-- Modal -->
    <div class="modal fade" id="modalTambahDataProkerTahunan">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Program Kerja</h5>
                    <button class="close" onclick="close_modal('modalTambahDataProkerTahunan')">
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
                                            <input type="text" class="form-control form-control-sm" id="prokTahunanTitle" name="prokTahunanTitle" placeholder="Judul Program Kerja" autocomplete="off">
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
                                            <input type="text" id="prokTahunanTime" name="prokTahunanTime" class="form-control form-control-sm" placeholder="YYYY">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="prokTahunanPIC"><b>PIC / Penanggung Jawab</b></label>
                                            <select name="prokTahunanGroupDivision" id="prokTahunanGroupDivision" style="width: 100%;" onchange="show_select('prokTahunanPIC', this.value,'')"></select> <br/>
                                            <select name="prokTahunanPIC" id="prokTahunanPIC" style="width: 100%;"></select>
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
                                                        <th class="text-center" style="vertical-align: middle;">&nbsp;</th>
                                                        <th class="text-center" style="vertical-align: middle;">No</th>
                                                        <th class="text-center" style="vertical-align: middle;">Judul</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12 text-right">
                                                <button type="button" class="btn btn-sm btn-primary" id="btnTambahBarisSubProk" value="1" onclick="tambahBaris('tblSubProk')">
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
                    <button type="button" class="btn btn-secondary" onclick="close_modal('modalTambahDataProkerTahunan')">Close</button>
                    <button type="button" class="btn btn-primary" id="btnTambahData" onclick="do_simpan(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/master/programKerja/tahunan/index.js') }}"></script>
@endpush