@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
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
        <div class="card shadow mb-5">
            <div class="card-header">
                <h4 class="no-margins">Table List Pengajuan Cuti / Izin / Tidak Masuk Kerja</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary" onclick="showModal('modal_pengajuan', 'add')">Buat Pengajuan</button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" style="width: 100%;" id="table_list_pengajuan">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">No</th>
                                        <th class="text-center align-middle" style="width: 20%;">Tanggal</th>
                                        <th class="text-left align-middle">Keterangan</th>
                                        <th class="text-center align-middle" style="width: 14%;">Jenis</th>
                                        <th class="text-center align-middle" style="width: 15%;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_pengajuan">
        <div class="modal-dialog modal-dialog-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row justify-content-between px-2 w-100 align-items-center">
                        <label class="no-margins" title="Form Buat Pengajuan">
                            <h4 class="no-margins">
                                Form Buat Pengajuan
                            </h4>
                        </label>
                        <button type="button" class="close" onclick="closeModal('modal_pengajuan')">&times;</button>
                    </div>
                </div>
                <div class="modal-body pb-2">
                    <form id="pgj_form" onsubmit="doSimpan(this.id, event)">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Uraian</label>
                                    <input type="text" class="form-control" id="pgj_title" name="pgj_title" placeholder="Tulis Uraian">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Jenis Pengajuan</label>
                                    <select class="form-control" id="pgj_type" name="pgj_type" style="width: 100%;" data-placeholder="Pilih Jenis Pengajuan"></select>
                                    {{-- <a href="#show_aturan" title="Lihat Aturan Jenis Pengajuan" onclick="showModal('modal_aturan_jenis_pengajuan','')">Lihat Aturan Pengajuan</a> --}}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Tanggal Pengajuan</label>
                                    <input type="text" class="form-control" id="pgj_date" name="pgj_date" placeholder="DD/MM/YYYY s/d DD/MM/YYYY" readonly style="background: white; cursor: pointer;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="font-weight-bold">Banyaknya Hari</label>
                                    <input type="text" class="form-control" id="pgj_count_day" name="pgj_count_day" placeholder="-- Hari" readonly>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex flex-row justify-content-end w-100 mb-2">
                            <button type="button" id="pgj_btn_aturan" class="btn btn-success" onclick="showModal('modal_aturan_jenis_pengajuan')">Lihat Aturan Pengajuan</button>
                            <button type="button" id="pgj_btn_tutup" class="btn btn-secondary mx-2" onclick="closeModal('modal_pengajuan')">Batal</button>
                            <button type="submit" value="" id="pgj_btn_simpan" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_aturan_jenis_pengajuan">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row w-100 align-items-center justify-content-between">
                        <h4 class="no-margins">Aturan Pengajuan</h4>
                        <button class="close" onclick="closeModal('modal_aturan_jenis_pengajuan')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis</th>
                                    <th>Lamanya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Cuti, Izin, Sakit</td>
                                    <td>3 Hari (Max)</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Cuti Hamil</td>
                                    <td>3 Bulan (Max)</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Keterlambatan</td>
                                    <td>Hari yang sama</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/activities/pengajuan/cuti/index.js') }}"></script>
@endpush