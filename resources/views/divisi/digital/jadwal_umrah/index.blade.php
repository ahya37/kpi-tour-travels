@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">

    <style>
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
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h3 class="no-margins">
                            <label class="no-margins font-weight-bold">List Jadwal Umrah</label>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-xxl-3 col-md-3 col-12">
                                <div class="form-group">
                                    <label for="filter_jadwal_umrah_tahun">Pilih Tahun</label>
                                    <select name="filter_jadwal_umrah_tahun" id="filter_jadwal_umrah_tahun" style="width: 100%;" class="form-control" onchange="showSelectedDetail(this.id, this.value)">
                                        <option selected disabled>Pilih Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="table_jadwal_umrah">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="text-center align-middle">Tour Code</th>
                                                <th class="text-center align-middle">Pembimbing</th>
                                                <th class="text-center align-middle">Keberangkatan</th>
                                                <th class="text-center align-middle">Kepulangan</th>
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
    </div>

    <div class="modal fade" id="modal_detail_jadwal_umrah">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="modal-title">
                        <label class="no-margins">Detail Tour Code <span id="modal_detail_jadwal_umrah_title"></span></label>
                    </h4>
                    <button class="close" onclick="closeModal('modal_detail_jadwal_umrah')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label class="no-margins">Tour Code</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" readonly id="jadwal_detail_tour_code" placeholder="Tour Code">
                            <input type="hidden" class="form-control" readonly id="jadwal_detail_uuid" placeholder="UUID">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label class="no-margins">Tour Leader</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" readonly id="jadwal_detail_tour_leader" placeholder="Tour Leader">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label class="no-margins">Tgl. Keberangkatan</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" readonly id="jadwal_detail_depature_date" placeholder="DD-MM-YYYY">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label class="no-margins">Tgl. Kepulangan</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" readonly id="jadwal_detail_arrival_date" placeholder="DD-MM-YYYY">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="no-margins"></label>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="table_detail_jadwal_umrah" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th>No</th>
                                            <th>Deskripsi</th>
                                            <th>Link</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" value="0" id="table_detail_btn_tambah_data" title="Tambah Kolom" onclick="addColumnTable('table_detail_jadwal_umrah', this.value, [])">Tambah Baris</button>
                    <button class="btn btn-primary" id="table_detail_btn_simpan_data" title="Simpan Data" value="" onclick="simpanData('modal_detail_jadwal_umrah', this.value)">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/digital/umrah/jadwal_umrah/index.jadwalumrah.js') }}"></script>
@endpush