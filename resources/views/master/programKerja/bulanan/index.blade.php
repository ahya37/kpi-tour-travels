@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    {{-- SELECT2 --}}
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    {{-- DATATABLES --}}
    <link href="https://cdn.datatables.net/v/bs4/dt-2.0.8/fc-5.0.1/fh-4.0.1/datatables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/fixedheader/4.0.1/css/fixedHeader.dataTables.min.css" rel="stylesheet">
    {{-- SWEETALERT --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    {{-- DROPZONE --}}
    <link href="{{ asset('assets/css/plugins/dropzone/basic.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    {{-- CUSTOM CSS --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/programKerja/bulanan/index.css') }}" rel="stylesheet">
@endpush

@section('breadcrumb')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $sub_title ?? '' }}</h2>
        </div>
    </div>
@endsection

@section('content')
    @php
        setlocale(LC_ALL, 'IND');
    @endphp
    <input type="hidden" name="current_uid" id="current_uid" value={{ Auth::user()->id }}>
    <input type="hidden" id="currentSubDivision">
    <input type="hidden" id="roleName" value={{ Auth::user()->getRoleNames()[0] }}>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h2 class="text-center">Pogram Kerja Bulan <span id="titleBulan">@php echo strftime('%B') @endphp</span> Tahun <span id="titleTahun">@php echo date('Y'); @endphp</span></h2>
                    </div>
                    <div class="ibox-content">
                        <div class="row mb-2">
                            <div class="col-sm-6 text-left">
                                <button type="button" class="btn btn-primary active" title="Calendar Global" id="btnCalendarGlobal" onclick="showCalendarButton('global')">Kalendar Semua Grup Divisi</button>
                                @if(Auth::user()->hasRole('admin'))
                                    <button type="butotn" class="btn btn-primary" title="Calendar Operasional" id="btnCalendarOperasional" onclick="showCalendarButton('operasional')">Kalendar Grup Divisi Operasional</button>
                                @endif
                                @if(Auth::user()->hasRole('operasional'))
                                    
                                <button type="butotn" class="btn btn-primary" title="Calendar Operasional" id="btnCalendarOperasional" onclick="showCalendarButton('operasional')">Kalendar Grup Divisi Operasional</button>
                                @endif
                            </div>
                            <div class="col-sm-6 text-right">
                                <input type="hidden" id="current_date">
                                <button type="button" class="btn btn-secondary" title="Filter Tanggal" id="btnFilter"><i class="fa fa-filter"></i> Filter</button>
                            </div>
                        </div>
                        <div class="row mb-2" style="padding-top: 8px;">
                            <div class="col-sm-12">
                                <div class="collapse" id="filterCalendar">
                                    <div class="card card-body">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <label>Tanggal Awal</label>
                                            </div>
                                            <div class="col-sm-3">
                                                <label>Tanggal Akhir</label>
                                            </div>
                                            @if(Auth::user()->hasRole('admin'))
                                                <div class="col-sm-3">
                                                    <label>Divisi</label>
                                                </div>
                                            @endif
                                            @if(Auth::user()->hasRole('operasional'))
                                                <div class="col-sm-3">
                                                    <label>Jadwal</label>
                                                </div>
                                                <div class="col-sm-2">
                                                    <label>Sub-Divisi</label>
                                                </div>
                                            @endif
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
                                                    <select id="groupDivisionName" style="width: 100%;" onchange="show_select_detail(this.id, this.value)"></select>
                                                </div>
                                            @endif
                                            @if(Auth::user()->hasRole('operasional'))
                                                <div class="col-sm-3">
                                                    <select name="jadwalUmrah" id="jadwalUmrah" style="width: 100%;"></select>
                                                </div>
                                                <div class="col-sm-2">
                                                    <select name="bagian" id="bagian" style="width: 100%;"></select>
                                                </div>
                                            @endif
                                            <div class="col-sm-1">
                                                <button class="btn btn-primary" id="btnFilterCari" onclick="showDataCalendar()" style="height: 37px;">Cari</button>
                                            </div>
                                        </div>
                                        @if(Auth::user()->hasRole('admin'))
                                            <div class="collapse" id="filterOperasional">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label>Program</label>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <label>Bagian</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <select name="jadwalUmrah" id="jadwalUmrah" style="width: 100%;"></select>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <select name="bagian" id="bagian" style="width: 100%;"></select>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-2">
                            <div class="col-sm-12">
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
                                <input type="hidden" name="currentRole" id="currentRole" value="{{ Auth::user()->name }}">
                                <select class="form-select" name="prokerTahunanID" id="prokerTahunanID" onchange="show_select_detail(this.id, this.value, true)" style="width:100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Sub-Program Kerja Tahunan</label>
                                <select class="form-select" name="subProkerTahunanSeq" id="subProkerTahunanSeq" style="width: 100%;" onchange="show_select_detail(this.id, this.value)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="collapseLainnya">
                        <div class="form-row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Jadwal Umrah / Haji</label>
                                    <select class="form-select" name="jadwalProgram" id="jadwalProgram" style="width: 100%;" onchange="show_select(`jadwalProgramUraian`, this.value, '', true)"></select>
                                </div>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Uraian Pekerjaan</label>
                                    <select class="form-select" name="jadwalProgramUraian" id="jadwalProgramUraian" style="width: 100%;" onchange="show_text(`jadwalProgramUraianPktSeq`, this.value)"></select>
                                    <input type="hidden" id="jadwalProgramUraianPktSeq">
                                    <input type="hidden" id="jadwalProgramUraianRulSeq">
                                </div>
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
                                <select class="form-select" name="prokerBulananPIC" id="prokerBulananPIC" style="width: 100%;"></select>
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
                    <div class="form-row mb-2" id="formProkerBulananTitle">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Uraian Pekerjaan</label>
                                <input type="text" name="prokerBulananTitle" id="prokerBulananTitle" class="form-control form-control-sm" placeholder="Uraian Pekerjaan" style="height: 37.5px;" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('operasional'))
                        <div class="form-row mb-2" id="formTanggalAktivitas_prokerBulanan">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Aktivitas</label>
                                    <input type="text" class="form-control form-control-sm tanggal" name="prokerBulananTanggal" id="prokerBulananTanggal" placeholder="DD/MM/YYYY">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" id="prokerBulananCheckSameDay">
                                        <label class="form-check-label" style="padding-top: 2px;">Di hari yang sama?</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Akhir Aktivitas</label>
                                    <input type="text" class="form-control form-control-sm tanggal" name="prokerBulananTanggalAkhir" id="prokerBulananTanggalAkhir" placeholder="DD/MM/YYYY">
                                </div>
                            </div>
                        </div>
                    @endif
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
                            <button type="button" class="btn btn-primary" id="btnTambahBaris" value="1" onclick="tambah_baris('tableDetailProkerBulanan', '', this.value)">Tambah Baris</button>
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
                <div class="modal-header">
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
    {{-- DROPZONE --}}
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    {{-- SELECT2 --}}
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    {{-- DATATABLE --}}
    <script src="https://cdn.datatables.net/v/bs4/dt-2.0.8/fc-5.0.1/fh-4.0.1/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.dataTables.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/4.0.1/js/dataTables.fixedHeader.min.js"></script>
    {{-- FULL CALENDAR AREA --}}
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/index.global.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/index.global.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/fullcalendar-6.1.13/dist/default/bootstrap4.index.global.min.js') }}"></script>
    {{-- SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    {{-- MOMENT AREA --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script>
    {{-- DATERANGEPICKER --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- CUSTOM JS --}}
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/programKerja/bulanan/index.js') }}"></script>
@endpush