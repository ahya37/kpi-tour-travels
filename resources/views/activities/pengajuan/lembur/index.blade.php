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
    <input type="hidden" value="{{ $data_emp['emp_id'] }}" id="emp_id">
    <input type="hidden" value="{{ $data_emp['emp_name'] }}" id="emp_name">
    <input type="hidden" value="{{ $data_emp['emp_divisi'] }}" id="emp_divisi">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="card shadow mb-5">
            <div class="card-header">
                <div class="d-flex flex-row align-items-center justify-content-between">
                    <h4 class="no-margins">List Pengajuan Lembur</h4>
                    <button class="btn btn-primary font-weight-bold" onclick="showModal('modal_buat_lemburan','')" title="Buat Pengajuan Lembur">Buat Pengajuan Lembur</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 col-lg-3 col-md-6">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold">Pilih Bulan</label>
                            <select id="pgj_lmb_select_month" style="width: 100%;" class="form-control form-select" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" style="width: 100%;" id="table_list_lembur">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">No</th>
                                        <th class="text-center align-middle" style="width: 20%;">Tanggal</th>
                                        <th class="text-left align-middle">Keterangan</th>
                                        <th class="text-center align-middle" style="width: 15%;">Status</th>
                                        <th class="text-center align-middle" style="width: 5%">Aksi</th>
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

    <div class="modal fade" id="modal_buat_lemburan">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="no-margins">
                        <label class="no-margins font-weight-bold">
                            Buat Pengajuan Lembur
                        </label>
                    </h4>
                    <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_buat_lemburan')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2 align-items-center d-none">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">ID Lemburan</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="ID Lemburan" readonly id="lmb_id">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Nama</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control d-none" placeholder="ID User" readonly id="lmb_name_id">
                            <input type="text" class="form-control" placeholder="Nama User" readonly id="lmb_name">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Divisi</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="Divisi" readonly id="lmb_divisi">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-top">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Keterangan</label>
                        </div>
                        <div class="col-sm-8 text-right">
                            <textarea class="form-control" placeholder="Keterangan Lemburan" id="lmb_keterangan" onkeyup="textToUppercase(this.id, this.value)" rows="4" style="resize: none;"></textarea>
                            <span>
                                <label class="no-margins font-weight-normal" title="Maksimal Karakter" id="lmb_keterangan_length">0/100</label>
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Tanggal</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="DD/MM/YYYY" id="lmb_date" style="background: white; cursor: pointer;" readonly>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Jam Mulai</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="lmb_start_time" placeholder="HH:mm" value="00:00" id="lmb_start_time" style="background: white; cursor: pointer;" readonly>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Jam Selesai</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="lmb_end_time" placeholder="HH:mm" value="00:00" id="lmb_end_time" style="background: white; cursor: pointer;" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btn_cancel_modal_buat_pengajuan" onclick="closeModal('modal_buat_lemburan')" title="Tutup Tampilan">Tutup</button>
                    <button class="btn btn-primary" id="btn_save_modal_buat_pengajuan" value="" onclick="simpanData('lemburan', this.value)" title="Simpan Data">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/activities/pengajuan/lembur/index.js') }}"></script>
@endpush