@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet"> --}}
    <link href="https://cdn.datatables.net/v/bs4/dt-2.0.8/fc-5.0.1/fh-4.0.1/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
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

    .dataTables_wrapper {
        padding-bottom: 0px;
        margin-top: -6px;
    }

    .ibox-title {
        padding: 15px;
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
    <input type="hidden" id="roleName" value={{ Auth::user()->getRoleNames()[0] }}>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-6 text-left">
                                <button type="button" class="btn btn-primary active" title="Calendar Global" id="btnCalendarGlobal" onclick="showCalendarButton('global')">Kalendar Semua Grup Divisi</button>
                                <button type="butotn" class="btn btn-primary" title="Calendar Operasional" id="btnCalendarOperasional" onclick="showCalendarButton('operasional')">Kalendar Grup Divisi Operasional</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="hidden" id="current_date">
                                <button type="button" class="btn btn-secondary" title="Filter Tanggal" id="btnFilter"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="collapse mt-4" id="filterCalendar">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label>Tanggal Awal</label>
                                        </div>
                                        <div class="col-sm-3">
                                            <label>Tanggal Akhir</label>
                                        </div>
                                        <div class="col-sm-3">
                                        @if(Auth::user()->hasRole('admin'))
                                            <label>Divisi</label>
                                        @endif
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            
                                            <input type="text" class="form-control date" id="prokerBulananStartDate" placeholder="DD/MM/YYYY" style="cursor: pointer; background: white; height: 38px;" readonly>
                                        </div>
                                        <div class="col-sm-3">
                                            
                                            <input type="text" class="form-control date" id="prokerBulananEndDate" placeholder="DD/MM/YYYY" style="cursor: pointer; background: white; height: 38px;" readonly>
                                        </div>
                                        @if(Auth::user()->hasRole('admin'))
                                            <div class="col-sm-3">
                                                <select id="groupDivisionName" style="width: 100%;"></select>
                                            </div>
                                        @endif
                                        <div class="col-sm-3">
                                            <button class="btn btn-primary" id="btnFilterCari" onclick="showDataCalendar()" style="height: 37px;">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div id="calendar" style="width: 100%;"></div>
                        <div id="calendarOperasional" style="width: 100%; display: none;">
                            <table class="table table-sm table-bordered table-striped table-hover" style="width: 200%;" id="tableCalendarOperasional">
                                <thead>
                                    <tr>
                                        <th>Aktivitas</th>
                                        @for($i = 0; $i < 31; $i++)
                                            <th class="text-center" style="vertical-align: middle;">{{ $i + 1 }}</th>
                                        @endfor
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-xl modal-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalTitle"></h4>
                    <button class="close" onclick="closeModal('modalForm')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Program Kerja Tahunan</label>
                                <select name="prokerTahunanID" id="prokerTahunanID" onchange="show_select_detail(this.id, this.value)" style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Sub-Program Kerja Tahunan</label>
                                <select name="subProkerTahunanSeq" id="subProkerTahunanSeq" style="width: 100%;" onchange="show_select_detail(this.id, this.value)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" style="display:none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Proker Bulanan ID</label>
                                <input type="text" class="form-control form-control-sm" name="prokerBulananID" id="prokerBulananID" placeholder="Proker Bulanan" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Grup Divisi</label>
                                <input type="hidden" class="form-control" id="prokerTahunanGroupDivisionID" name="prokerTahunanGroupDivisionID">
                                <input type="text" class="form-control form-control-sm" id="prokerTahunanGroupDivisionName" name="prokerTahunanGroupDivisionName" readonly placeholder="Grup Divisi" style="height: 37.5px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PIC / Penanggung Jawab</label>
                                <select name="prokerBulananPIC" id="prokerBulananPIC" style="width: 100%;"></select>
                            </div>
                        </div>
                        <div class="col-md-6" style="display: none;">
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
                                <label>Uraian Pekerjaan</label>
                                <input type="text" name="prokerBulananTitle" id="prokerBulananTitle" class="form-control form-control-sm" placeholder="Uraian Pekerjaan" style="height: 37.5px;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formWaktuAktivitias_prokerBulanan">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Waktu Awal Aktivitas</label>
                                <input type="text" name="prokerBulananStartTime" id="prokerBulananStartTime" class="form-control form-control-sm waktu" placeholder="HH:MM:SS" style="height: 37.5px;" onclick="this.setSelectionRange(0, 2)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Waktu Akhir Aktivitas</label>
                                <input type="text" name="prokerBulananEndTime" id="prokerBulananEndTime" class="form-control form-control-sm waktu" placeholder="HH:MM:SS" style="height: 37.5px;" onclick="this.setSelectionRange(0, 2)">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="prokerBulananDesc" id="prokerBulananDesc" class="form-control form-control-sm" rows="4" placeholder="Tulis keterangan jika ada.."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formTableDetailProkerBulanan">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Detail Uraian Tugas</label>
                                <table class="table table-sm" id="tableDetailProkerBulanan">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">Aksi</th>
                                            <th class="text-center" style="vertical-align: middle;">Jenis Pekerjaan</th>
                                            <th class="text-center" style="vertical-align: middle;">Target / Sasaran</th>
                                            <th class="text-center" style="vertical-align: middle;">Hasil</th>
                                            <th class="text-center" style="vertical-align: middle;">Evaluasi</th>
                                            <th class="text-center" style="vertical-align: middle;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" id="btnTambahBaris" onclick="tambah_baris('tableDetailProkerBulanan','')" value="1">Tambah Baris</button>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formUpload">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>File Dokumen / Gambar Aktivitas <small class="text-danger">* Jika ada</small></label>
                                <div class="dropzone" id="myDropzone"></div>
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

    <div class="modal fade" id="modalAktivitas">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Lihat Aktivitas User</h4>
                    <button type="button" class="close" onclick="closeModal('modalAktivitas')">
                        <span aria-hidden="true" >&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>ID Program Kerja Bulanan</label>
                                <input type="text" class="form-control" id="prokerBulananID_Activity" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-sm table-striped table-bordered" id="tableActivityUser">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: center;">No</th>
                                        <th class="text-center" style="vertical-align: center;">Uraian Pekerjaan</th>
                                        <th class="text-center" style="vertical-align: center;">Dibuat Oleh</th>
                                        <th class="text-center" style="vertical-align: center;">Waktu Pengerjaan</th>
                                        <th class="text-center" style="vertical-align: center;">Bukti</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalAktivitas')" title='Tutup Form'>Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-2.0.8/fc-5.0.1/fh-4.0.1/datatables.min.js"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    {{-- FULL CALENDAR AREA --}}
    <script src="{{ asset('assets/js/plugins/fullcalendar/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/index.global.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/index.global.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/bootstrap4.index.global.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script src="{{ asset('js/master/programKerja/bulanan/index.js') }}"></script>
@endpush