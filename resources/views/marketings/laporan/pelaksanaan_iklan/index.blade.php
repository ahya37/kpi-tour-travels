@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
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
                        <button type="button" class="btn btn-sm btn-primary" onclick="show_modal('modalAdd')">Tambah Data</button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTable" id="tableLaporanIklan" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="vertical-align: middle;">No</th>
                                        <th class="text-center" style="vertical-align: middle;">Materi</th>
                                        <th class="text-center" style="vertical-align: middle;">Periode</th>
                                        <th class="text-center" style="vertical-align: middle;">Total Respon</th>
                                        <th class="text-center" style="vertical-align: middle;">Biaya</th>
                                        <th class="text-center" style="vertical-align: middle;">Status Iklan</th>
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

    <div class="modal fade" id="modalAdd">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        <b>Tambah Data Laporan Pelaksanaan Iklan</b>
                    </h4>
                </div>
                <div class="modal-body">
                    <form method="POST" id="formPost">
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><b>Materi Iklan</b></label>
                                    <input type="text" id="adsNameAdd" name="adsNameAdd" class="form-control" placeholder="Materi Iklan">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label><b>Tgl. Mulai</b></label>
                                    <input type="text" id="adsStartDateAdd" name="adsStartDateAdd" class="form-control" placeholder="DD/MM/YYYY" onchange="hitungTanggal(this.value, $('#adsPeriodeAdd').val())">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label><b>Lamanya</b></label>
                                    <div class="input-group">
                                        <input type="number" id="adsPeriodeAdd" name="adsPeriodeAdd" step="1" min="0" max="3600" placeholder="" class="form-control" value="0" onclick="this.select()" onkeyup="hitungTanggal($('#adsStartDateAdd').val(), this.value)" onblur="ubahForm(this.id, this.val, 'ubah_ke_nol')">
                                        <span class="input-group-addon"><b>Hari</b></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-7">
                                <div class="form-group">
                                    <label><b>Tgl. Berakhir</b></label>
                                    <input type="text" id="adsEndDateAdd" name="adsEndDateAdd" class="form-control" placeholder="DD/MM/YYYY" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <label><b>Penyebaran Iklan</b></label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        Jml. Jangkauan
                                        <input type="text" id="adsScopeAdd" name="adsScopeAdd" class="form-control text-right" placeholder="Jml. Penyebaran Iklan" value="0.00" onclick="this.select()" onkeyup="ubahForm(this.id, this.value, 'ubah_ke_ribuan')" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol_1')">
                                    </div>
                                    <div class="col-sm-6">
                                        Jml. Penayangan Iklan
                                        <input type="text" id="adsShowAdd" name="adsShowAdd" class="form-control text-right" placeholder="Jml. Penayangan Iklan" value="0.00" onclick="this.select()" onkeyup="ubahForm(this.id, this.value, 'ubah_ke_ribuan')" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol_1')">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <label><b>Respon Pengguna</b></label>
                                <table class="table table-bordered" style="width: 100%;" id="tableResponseUser">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="vertical-align: middle;">Aksi</th>
                                            <th class="text-center" style="vertical-align: middle;">Jenis</th>
                                            <th class="text-center" style="vertical-align: middle;">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <a href="#" id="btnResponseUser" value="1" style="padding-top: -100px;">
                                    <i class="fa fa-plus-circle"></i> Tambah Data
                                </a>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Total Responden</b></label>
                                    <input type="text" id="adsTotalResponseAdd" name="adsTotalResponseAdd" class="form-control text-right" placeholder="Total Responden" value="0.00" onclick="this.select()" onkeyup="ubahForm(this.id, this.value, 'ubah_ke_ribuan')" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol_1')">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><b>Total Responden (Gender)</b></label>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            Laki-Laki
                                            <div class="input-group">
                                                <input type="number" id="adsResponseMaleAdd" name="adsResponseMaleAdd" class="form-control text-right" placeholder="Response Laki-laki" value="0" step="1" min="0" max="100" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol')" onclick="this.select()">
                                                <span class="input-group-addon"><b>%</b></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            Perempuan
                                            <div class="input-group">
                                                <input type="number" id="adsResponseFemaleAdd" name="adsResponseFemaleAdd" class="form-control text-right" placeholder="Response Laki-laki" value="0" step="1" min="0" max="100" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol')" onclick="this.select()">
                                                <span class="input-group-addon"><b>%</b></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><b>Status Iklan</b></label>
                                    <select id="adsStatusAdd" name="adsStatusAdd" class="select2_demo_1 form-control" style="width: 100%;">
                                        <option selected disabled>Pilih Status</option>
                                        <option value="1">Aktif</option>
                                        <option value="0">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label><b>Biaya</b></label>
                                    <input type="text" id="adsPriceAdd" name="adsPriceAdd" class="form-control text-right" placeholder="Biaya" value="0.00" onclick="this.select()" onkeyup="ubahForm(this.id, this.value, 'ubah_ke_ribuan')" onblur="ubahForm(this.id, this.value, 'ubah_ke_nol_1')">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="close_modal('modalAdd')">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="do_simpan('simpan')">Simpan</button>
                </div>
            </div>
        </div>
    </div>  
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/laporan/pelaksanaan_iklan/index.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
@endpush