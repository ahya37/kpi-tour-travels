@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
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
                        <button type="button" class="btn btn-primary" onclick="show_modal('modalForm','add','')">Tambah Data</button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" id="tableGroupDivision" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">No</th>
                                        <th class="text-center" style="vertical-align: middle;">Name Group Division</th>
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

    <div class="modal fade" id="modalForm">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    Create Data Group Division
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <h4 class="pt-0.5">Name</h4>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" class="form-control form-control-sm" name="groupDivisionID" id="groupDivisionID" placeholder="Group Division ID">
                            <input type="text" class="form-control form-control-sm" name="groupDivisionName" id="groupDivisionName" placeholder="Name Group Division">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12 text-right">
                            <button class="btn btn-secondary" onclick="close_modal('modalForm')">Batal</button>
                            <button class="btn btn-primary" id="btnSimpan" value="add" onclick="do_save(this.value)">Simpan</button>
                        </div>
                    </div>
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
    <script src="{{ asset('js/master/groupDivision/group_division_index.js') }}"></script>
@endpush