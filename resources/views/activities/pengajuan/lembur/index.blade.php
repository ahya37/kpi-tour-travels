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
                <h4 class="no-margins">List Pengajuan Lembur</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary" onclick="showModal('modal_buat_lemburan','')">Buat Pengajuan Lembur</button>
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
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row align-items-center justify-content-between w-100">
                        <h4 class="no-margins">
                            <label class="no-margins">Buat Pengajuan Lembur</label>
                        </h4>
                        <button class="close" onclick="closeModal('modal_buat_lemburan')">&times;</button>
                    </div>
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
                            <input type="hidden" class="form-control" placeholder="ID user" readonly id="lmb_name_id">
                            <input type="text" class="form-control" placeholder="Nama" readonly id="lmb_name">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Bagian</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="Bagian" readonly id="lmb_divisi">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-4">
                            <label class="no-margins font-weight-bold">Untuk</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" placeholder="Keterangan" readonly id="lmb_keterangan" value="Untuk Lemburan">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hovered table-bordered" id="table_list_lembur_detail" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 10%">Aksi</th>
                                            <th class="text-center align-middle" style="width: 20%;">Tanggal</th>
                                            <th class="text-center align-middle">Uraian Pekerjaan</th>
                                            <th class="text-center align-middle" style="width: 20%;">Jam Mulai</th>
                                            <th class="text-center align-middle" style="width: 20%;">Jam Selesai</th>
                                            <th class="text-center align-middle" style="width: 10%">Persetujuan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="btn_tambah_baris" value="1" onclick="addRow('table_list_lembur_detail', this.value, '')">Tambah Baris</button>
                    |
                    <button class="btn btn-secondary" id="btn_cancel" onclick="closeModal('modal_buat_lemburan')">Batal</button>
                    <button class="btn btn-primary" id="btn_simpan" value="" onclick="simpanData('lemburan', this.value)">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/activities/pengajuan/lembur/index.js') }}"></script>
    {{-- <script src="{{ asset('js/activities/pengajuan/cuti/index.js') }}"></script> --}}
@endpush