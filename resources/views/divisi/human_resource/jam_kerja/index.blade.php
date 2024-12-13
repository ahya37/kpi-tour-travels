@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/plugins/switchery/switchery.css') }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <style>
        input[type="text"]:read-only {
            cursor: pointer;
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
        {{-- DESKTOP VIEW --}}
        <div class="col-12 d-sm-block d-none">
            <div class="card">
                <div class="card-header d-flex flex-row align-items-center justify-content-between w-100 bg-success">
                    <h4 class="no-margins">
                        <label class="font-weight-bold no-margins">Jam Kerja Yang Sedang Berjalan</label>
                    </h4>
                    <i class="fa fa-clock"></i>
                </div>
                <div class="card-body">
                    <div id="current_time_loading" class="d-flex flex-column align-items-center">
                        <i class="fa fa-spinner fa-spin fa-1x"></i> <label class="no-margins">Data Sedang Dimuat</label>
                    </div>
                    <div id="current_time" class="d-none">
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Senin</label>
                                <label class="font-weight-normal" id="day1"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="col-12 align-items-center text-center d-flex flex-column">
                                    <label class="font-weight-bold">Selasa</label>
                                    <label class="font-weight-normal" id="day2"></label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Rabu</label>
                                <label class="font-weight-normal" id="day3"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Kamis</label>
                                <label class="font-weight-normal" id="day4"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Jumat</label>
                                <label class="font-weight-normal" id="day5"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Sabtu</label>
                                <label class="font-weight-normal" id="day6"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 align-items-center text-center d-flex flex-column">
                                <label class="font-weight-bold">Minggu</label>
                                <label class="font-weight-normal" id="day7"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- MOBILD VIEW --}}
        <div class="col-12 d-sm-none d-block">
            <div class="card">
                <div class="card-header d-flex flex-row align-items-center justify-content-between w-100 bg-success">
                    <h4 class="no-margins">
                        <label class="font-weight-bold no-margins">Jam Kerja Yang Sedang Berjalan</label>
                    </h4>
                    <i class="fa fa-clock"></i>
                </div>
                <div class="card-body" id="list_jam_kerja">
                    {{-- <div class="d-flex flex-row align-items-center w-100 justify-content-center">
                        <span><i class="fa fa-spinner fa-spin"></i> <label class="font-weight-bold">Data Sedang Dimuat</label> </span>
                    </div> --}}
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Senin</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_1_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Selasa</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_2_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Rabu</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_3_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Kamis</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_4_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Jumat</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_5_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold">Sabtu</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_6_mobile"></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <label class="font-weight-bold text-danger">Minggu</label>
                        </div>
                        <div class="col-8">
                            <label class="font-weight-bold" id="day_7_mobile"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-4">
            <div class="card">
                <div class="card-header bg-primary d-flex flex-row align-items-center justify-content-between">
                    <h4 class="card-title no-margins">
                        <label class="no-margins font-weight-bold">Tabel Jam Kerja</label>
                    </h4>
                    <button class="btn btn-sm btn-primary" title="Tambah Data Jam Kerja" value="add" onclick="showModal('modal_jam_kerja', this.value, '')">
                        <i class="fa fa-plus"></i> Tambah Data
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-border table-hover" id="table_jam_kerja" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">No</th>
                                    <th class="text-center align-middle" rowspan="2">Tgl. Mulai</th>
                                    <th class="text-center align-middle" rowspan="2">Tgl. Akhir</th>
                                    <th class="text-center align-middle" rowspan="1" colspan="7">Hari</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Senin</span>
                                        <span class="d-block d-sm-none">Sen</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Selasa</span>
                                        <span class="d-block d-sm-none">Sel</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Rabu</span>
                                        <span class="d-block d-sm-none">Rab</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Kamis</span>
                                        <span class="d-block d-sm-none">Kam</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Jumat</span>
                                        <span class="d-block d-sm-none">Jum</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Sabtu</span>
                                        <span class="d-block d-sm-none">Sab</span>
                                    </th>
                                    <th class="text-center align-middle">
                                        <span class="d-none d-sm-block">Minggu</span>
                                        <span class="d-block d-sm-none">Min</span>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_jam_kerja">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="modal-title no-margins">
                    <label class="font-weight-bold no-margins">Form Setting Jam Kerja</label>
                </h4>
                <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_jam_kerja')">&times;</button>
            </div>
            <div class="modal-body">
                {{-- FORM MOBILE --}}
                <div class="d-block d-sm-none">
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Tanggal Awal</label>
                                <input type="text" class="form-control" placeholder="DD/MM/YYYY" readonly id="d_start_mobile">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Tanggal Akhir</label>
                                <input type="text" class="form-control" placeholder="DD/MM/YYYY" readonly id="d_end_mobile">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Senin</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d1_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d1_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Selasa</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d2_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d2_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Rabu</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d3_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d3_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Kamis</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d4_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d4_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Jumat</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d5_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d5_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Sabtu</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d6_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d6_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold no-margins">Minggu</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d7_start_mobile">
                                    </div>
                                    <div class="col-6">
                                        <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d7_end_mobile">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- FORM DESKTOP --}}
                <div class="d-none d-sm-block">
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Tanggal Awal</label>
                        </div>
                        <div class="col-8">
                            <input type="text" class="form-control" placeholder="DD/MM/YYYY" readonly id="d_start_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Tanggal Akhir</label>
                        </div>
                        <div class="col-8">
                            <input type="text" class="form-control" placeholder="DD/MM/YYYY" readonly id="d_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Senin</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d1_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d1_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Selasa</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d2_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d2_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Rabu</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d3_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d3_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Kamis</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d4_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d4_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Jumat</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d5_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d5_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Sabtu</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d6_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d6_end_desktop">
                        </div>
                    </div>
                    <div class="row mb-2 flex-row align-items-center">
                        <div class="col-4">
                            <label class="font-weight-bold no-margins">Minggu</label>
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d7_start_desktop">
                        </div>
                        <div class="col-4">
                            <input type="text" class="form-control" placeholder="HH:MM" readonly id="t_d7_end_desktop">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" title="Tutup Tampilan" onclick="closeModal('modal_jam_kerja')">Batal</button>
                <button class="btn btn-primary" title="Simpan Data" id="btn_form_jam_kerja" onclick="doSimpan('form_jam_kerja', this.value)">Simpan</button>
            </div>
        </div>
    </div>
</div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/human_resource/jam_kerja/index.jamkerja.js') }}"></script>
@endpush