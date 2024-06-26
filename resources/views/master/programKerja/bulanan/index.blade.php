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
    .fc-daygrid-event-dot {
        border: 4px solid #1de5bd;
    }

    .fc-daygrid-event-dot {
        color: #FFF;
    }

    .fc-event-time {
        color: #FFF;
        font-size: 8pt;
    }
    
    .fc-event-title {
        color: #FFF;
        font-size: 8pt;
    }

    a.fc-event:hover {
        /* text-decoration: underline; */
        background-color: #159178;
        border-color: #159178;
    }

    .fc-day:hover {
        cursor: pointer;
    }
    label {
        font-weight: bold;
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
                        
                    </div>
                    <div class="ibox-content">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-lg modal-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle"></h4>
                    <button class="close" onclick="closeModal('modalForm')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Proram Kerja Tahunan</label>
                                <select name="prokerTahunanID" id="prokerTahunanID" class="form-control form-control-sm select2"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Group Division</label>
                                <input type="hidden" class="form-control form-control-sm" id="prokerTahunanGroupDivisionID" name="prokerTahunanGroupDivisionID">
                                <input type="text" class="form-control form-control-sm" id="prokerTahunanGroupDivisionName" name="prokerTahunanGroupDivisionName" readonly placeholder="Group Division">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sub Division</label>
                                <input type="hidden" class="form-control form-control-sm" id="prokerTahunanSubDivisionID" name="prokerTahunanSubDivisionID">
                                <input type="text" class="form-control form-control-sm" id="prokerTahunanSubDivisionName" name="prokerTahunanSubDivisionName" readonly placeholder="Sub Division">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul Program Kerja</label>
                                <input type="text" name="prokerBulananTitle" id="prokerBulananTitle" class="form-control form-control-sm" placeholder="Judul Program">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancel" onclick="closeModal('modalForm')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/index.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script>
    <script src="{{ asset('js/master/programKerja/bulanan/index.js') }}"></script>
@endpush