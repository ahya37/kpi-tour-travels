@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
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
                        {{-- <a href="{{ route('employees.add') }}" class="btn btn-primary">Add Data</a> --}}
                        <button type="button" class="btn btn-primary" title='Menambahkan Data Baru' onclick="show_modal('modalForm','add','')">Tambah Data</button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" id="tableEmployees" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">No</th>
                                        <th class="text-center" style="vertical-align: middle;">Nama</th>
                                        <th class="text-center" style="vertical-align: middle;">Divisi</th>
                                        <th class="text-center" style="vertical-align: middle;">Aksi</th>
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

    <!-- Modal -->
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Data Employee</h5>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2" style="display: none;">
                        <div class="col-sm-4">
                            <h4>ID</h4>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" placeholder="ID Employee" id="empIDAdd" readonly>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4>Nama Lengkap</h4>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control form-control-sm" placeholder="Nama Lengkap" id="empNameAdd" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4>Group Divisi</h4>
                        </div>
                        <div class="col-sm-8">
                            <select id="empGdIDAdd" class="form-control form-control-sm" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4>Role User</h4>
                        </div>
                        <div class="col-sm-8">
                            <select id="empRoleAdd" class="form-control form-control-sm" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <h4>ETA Username</h4>
                        </div>
                        <div class="col-sm-8">
                            <input id="empUsernameAdd" type="text" class="form-control form-control-sm" placeholder="Estimasi Username" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="close_modal('modalForm')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="do_simpan(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/employee/employee.index.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
@endpush