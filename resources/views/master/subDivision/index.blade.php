@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/ladda/ladda-themeless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
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
                <div class="ibox ">
                    <div class="ibox-title">
                        <button type="button" class="btn btn-primary" onclick="show_modal('modalSubDivisionAdd')">Tambah Data</button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" id="tableSubDivision" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">No</th>
                                        <th class="text-center" style="vertical-align: middle;">Name</th>
                                        <th class="text-center" style="vertical-align: middle;">Group Division</th>
                                        <th class="text-center" style="vertical-align: middle;">Created At</th>
                                        <th class="text-center" style="vertical-align: middle;">Action</th>
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

    {{-- MODAL TAMBAH DATA --}}
    <div class="modal fade" id="modalSubDivisionAdd">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Tambah Data Sub Division
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4 class="pt-0.5">Grup Divisi</h4>
                        </div>
                        <div class="col-sm-8">
                            <select class="form-control form-control-sm" id="groupDivisionID" style="width: 100%;">
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4 class="pt-0.5">Nama</h4>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" placeholder="Sub-Divisi Nama" id="subDivisionName" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-sm btn-secondary" onclick="close_modal('modalSubDivisionAdd')">Batal</button>
                    <button class="btn btn-sm btn-primary" onclick="do_save('simpan')">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal EDIT -->
    <div class="modal fade" id="modalSubDivisionEdit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Data Sub Division</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4 class="pt-0.5">Group Division</h4>
                        </div>
                        <div class="col-sm-8">
                            <select id="groupDivisionIDEdit" class="form-control form-control-sm" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-sm-4">
                            <h4 class="pt-0.5">Name</h4>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control form-contorl-sm" id="subDivisionIDEdit">
                            <input type="text" class="form-control form-control-sm" id="subDivisionNameEdit">
                        </div></div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button type="button" class="btn btn-sm btn-secondary" title='Batal' onclick="close_modal('modalSubDivisionEdit','')">Batal</button>
                            <button type="button" class="btn btn-sm btn-primary" title='Update Data' onclick="do_save('edit')">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('prepend-script')
 
@endpush

@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/loaders.js') }}"></script>
    <script src="{{ asset('js/ladda-button.js') }}"></script>
    <script src="{{ asset('js/master/subDivision/subDivision.index.js') }}"></script>
@endpush