@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    {{-- SELECT2 --}}
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    {{-- DATATABLES --}}
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    {{-- SWEETALERT2 --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    {{-- DROPZONE --}}
    <link href="{{ asset('assets/css/plugins/dropzone/basic.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    {{-- CUSTOM CSS --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/programKerja/harian/index.harian.css') }}">

    <style type="text/css">
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
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <button class="btn btn-primary" id="btnTambahData" onclick="showModal('modalForm', 'add', '')">Tambah Data</button>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button class="btn btn-secondary" id="btnFilter" onclick="showFilter()" data-toggle="collapse" data-target="#contentId" aria-expanded="false"
                                    aria-controls="contentId">Filter</button>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="collapse" id="contentId">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-sm-2"><label>Bulan</label></div>
                                    <div class="col-sm-3" style="@php echo Auth::user()->hasRole('admin') ? '' : 'display: none;' @endphp"><label>Role</label></div>
                                    <div class="col-sm-2"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <select name="filterHarianBulan" id="filterHarianBulan" style="width: 100%;"></select>
                                    </div>
                                    <div class="col-sm-3" style="@php echo Auth::user()->hasRole('admin') ? '' : 'display: none;' @endphp">
                                        <select name="filterHarianRole" id="filterHarianRole" style="width: 100%;"></select>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="filterBtnCari" style="height: 37.5px;" onclick="showFilteredData()">Cari</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-sm-12">
                                <table class="table table-sm table-bordered table-striped" id="tableListHarian">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">No</th>
                                            <th class="text-center" style="vertical-align: middle;">Uraian</th>
                                            <th class="text-center" style="vertical-align: middle;">Tgl. Aktivitas</th>
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
    </div>
    
    {{-- MODAL CREATE DATA --}}
    <!-- Modal -->
    <div class="modal fade" id="modalForm">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" >
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Aktivitas Harian</h4>
                    <button type="button" class="close" onclick="closeModal('modalForm')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="currentRole" value="@php echo Auth::user()->getRoleNames()[0] @endphp">
                    <div class="form-row mb-2" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Proker Harian ID</label>
                                <input type="text" class="form-control form-control-sm" style="height: 37.5px;" readonly placeholder="ID" id="programKerjaHarianID" name="programKerjaHarianID">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Aktivitas</label>
                                <input type="text" class="form-control form-control-sm" style="height: 37.5px; cursor: pointer; background:white;" readonly placeholder="DD/MM/YYYY" name="programKerjaHarianTanggal" id="programKerjaHarianTanggal">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Awal Aktivitas</label>
                                <input type="text" class="form-control form-control-sm waktu" placeholder="HH:mm:ss" name="programKerjaHarianWaktuMulai" id="programKerjaHarianWaktuMulai" onclick="this.setSelectionRange(0, 2)" onfocus="this.setSelectionRange(0, 2)">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>Waktu Akhir Aktivitas</label>
                                <input type="text" class="form-control form-control-sm waktu" placeholder="HH:mm:ss" name="programKerjaHarianWaktuAkhir" id="programKerjaHarianWaktuAkhir" onclick="this.setSelectionRange(0, 2)" onfocus="this.setSelectionRange(0, 2)">
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-2" style="@php echo Auth::user()->hasRole('umum') ? '' : 'display: none;' @endphp">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Divisi</label>
                                <select name="programKerjaHarianDivisi" id="programKerjaHarianDivisi" style="width: 100%;" onchange="showSelect(`programKerjaTahunanID`, this.value, '', true)"></select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-2" style="@php echo Auth::user()->hasRole('umum') ? '' : 'display: none;' @endphp">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program Kerja Tahunan</label>
                                <select name="programKerjaTahunanID" id="programKerjaTahunanID" style="width: 100%;" onchange="showSelect(`programKerjaBulananID`, this.value, '', true)"></select>
                            </div>
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program Bulanan</label>
                                <select name="programKerjaBulananID" id="programKerjaBulananID" style="width: 100%;" onchange="showSelect('programKerjaBulananAktivitas', this.value, '', true)"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formProgramKerjaBulananAktivitas">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <select name="programKerjaBulananAktivitas" id="programKerjaBulananAktivitas" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formProgramKerjaBulananAktivitasText" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pekerjaan</label>
                                <input type="text" class="form-control form-control-sm" name="programKerjaBulananAktivitasText" id="programKerjaBulananAktivitasText" placeholder="Jenis Pekerjaan" style="height: 37.5px;">
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian Tugas</label>
                                <textarea name="programKerjaHarianJudul" id="programKerjaHarianJudul" class="form-control form-control-sm" rows="4" placeholder="Tulis Uraian Pekerjaan Disini.."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-2" id="formListUpload">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>List File</label>
                                <table class="table table-sm" style="width: 100%;" id="tableListFile">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">No</th>
                                            <th class="text-center" style="vertical-align: middle;">Nama File</th>
                                            <th class="text-center" style="vertical-align: middle;">Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
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
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalForm')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" onclick="doSimpan(this.value)">Simpan</button>
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
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    {{-- SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    {{-- MOMENT --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{-- DATERANGEPICKER --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- CUSTOM JS --}}
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/programKerja/harian/index.js') }}"></script>
@endpush