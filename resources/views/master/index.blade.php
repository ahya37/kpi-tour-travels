@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">
    
    <style>
        input[type=text] {
            height: 38px;
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
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="no-margins">Master Jabatan</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 div col-md-4 col-sm-6 col-12 mb-xl-0 mb-2" style="cursor: pointer;" title="Role System" onclick="showModal('modal_role')">
                <div class="card card-body bg-primary" title="Grup Division">
                    <div class="row align-items-center justify-content-center">
                        <h2 class="no-margins font-weight-bold">Role System</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 div col-md-4 col-sm-6 col-12 mb-xl-0 mb-2" style="cursor: pointer;" title="Lihat Divisi">
                <div class="card card-body bg-primary" title="Grup Division">
                    <div class="row align-items-center justify-content-center">
                        <h2 class="no-margins font-weight-bold">Divisi</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 div col-md-4 col-sm-6 col-12 mb-xl-0 mb-2" style="cursor: pointer;" title="Lihat Jabatan">
                <div class="card card-body bg-primary" title="Grup Division">
                    <div class="row align-items-center justify-content-center">
                        <h2 class="no-margins font-weight-bold">Jabatan</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 div col-md-4 col-sm-6 col-12 mb-xl-0 mb-2" style="cursor: pointer;" title="Lihat Karyawan">
                <div class="card card-body bg-primary" title="Grup Division">
                    <div class="row align-items-center justify-content-center">
                        <h2 class="no-margins font-weight-bold">Karyawan</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_role">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins">
                        <label class="no-margins font-weight-bold">Role System</label>
                    </h4>
                    <button class="close" onclick="closeModal('modal_role')" title="Tutup Tamilan">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <button class="btn btn-success btn-sm" data-toggle="collapse" href="#collapse_form_role" onclick="collapseAction('collapse_form_role', 'add', '')"><i class="fa fa-plus"></i> Tambah Data</button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="collapse" id="collapse_form_role">
                                <form id="form_role">
                                    <div class="form-group">
                                        <label for="role_name" class="font-weight-bold no-margins">Nama</label>
                                        <input type="hidden" id="fr_id" name="fr_id" placeholder="ID Role">
                                        <input type="text" class="form-control" id="fr_name" name="fr_name" placeholder="Nama Role" autocomplete="off">
                                    </div>
                                    <div class="row text-right">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary btn-sm" value="" id="btn_act_fr" onclick="doSaveTransaction('form_role', this.value, )"><i class="fa fa-save"></i> Simpan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover table-bordered" style="width: 100%;" id="table_role">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Role</th>
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
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/index.dashboard.js') }}"></script>
@endpush