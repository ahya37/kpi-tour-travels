@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        label {
            font-weight: bold;
        }
        .form-control-sm {
            height: 37px;
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
        <div class="ibox">
            <div class="ibox-title" style="padding: 16px;">
                <div class="row">
                    <div class="col-sm-6 text-left">
                        <button class="btn btn-primary" onclick="showModal('modalForm', 'add', '')">Tambah Data</button>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-success" title='Muat Ulang Data' onclick='showTable(`table_list_aturan`, `%`)'><i class='fa fa-undo'></i></button>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-hover no-margins table-striped" id="table_list_aturan" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Uraian Pekerjaan</th>
                                    <th class="text-center">Durasi</th>
                                    <th class="text-center">SLA</th>
                                    <th>PIC</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Tambah Aturan Program Kerja Baru</h4>
                        <button type="button" class="close" onclick="closeModal('modalForm')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <div class="modal-body">
                    <div class="form-group" style="display: none;">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>Rules ID</label>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="rulesID" id="rulesID" class="form-control form-control-sm text-center" placeholder="Rules ID" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>Uraian</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="ruleDescription" id="ruleDescription" class="form-control form-control-sm" placeholder="Uraian Pekerjaan" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>Program Kerja Tahunan</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="rulePktID" id="rulePktID" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>PIC</label>
                            </div>
                            <div class="col-sm-9">
                                <select name="rulePicID" id="rulePicID" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>Durasi Pekerjaan</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" name="ruleDurationDay" id="ruleDurationDay" class="form-control form-control-sm" min="0" max="999" step="1" placeholder="Jml. Hari" value="0" onfocus="this.select()" onclick="this.select()">
                            </div>
                            <div class="col-sm-2">
                                <input type="text" class="form-control form-control-sm" value="Hari" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                <label>SLA</label>
                            </div>
                            <div class="col-sm-1">
                                <input type="text" class="form-control form-control-sm text-center" value="H" readonly>
                            </div>
                            <div class="col-sm-2">
                                <select name="rulePlusMin" id="rulePlusMin" class="text-center" style="width: 100%;" onchange="showSelect('ruleCondition', this.value, '', '')"></select>
                            </div>
                            <div class="col-sm-2">
                                <input type="number" name="rulesSLADay" id="rulesSLADay" class="form-control form-control-sm" placeholder="Hari" min="0" max="999" step="1" value="0" onclick="this.select()" onfocus="this.select()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-row">
                            <div class="col-sm-3 pt-2">
                                Kondisi
                            </div>
                            <div class="col-sm-9">
                                <select name="ruleCondition" id="ruleCondition" style="width: 100%;">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalForm', 'add')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="simpanData(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    {{-- MOMENT AREA --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script>
    {{-- DATERANGEPICKER --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src={{ asset('js/divisi/operasional/aturan/index.operasional.aturan.js') }}></script>
@endpush