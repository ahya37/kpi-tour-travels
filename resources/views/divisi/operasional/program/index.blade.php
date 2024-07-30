@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')

    <style>
        label {
            font-weight: bold;
        }
        .form-control-sm {
            height: 36.5px;
        }
    </style>
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
        <div class="ibox">
            <div class="ibox-title" style="padding: 16px;">
                <div class="row">
                    <div class="col-sm-6 text-left">
                        {{-- <button class="btn btn-primary" onclick="showModal('modalForm', '', 'add')">Tambah Data</button> --}}
                        <button class="btn btn-primary" onclick="showModalV2('modalFormV2', '', 'add')">Tambah Data</button>
                        <button class="btn btn-primary" onclick="showModalTourCode('modalShowTourCode', 'add')">Tambah Data Tour Code</button>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button class="btn btn-secondary" data-toggle="collapse" href="#FilterCollapse" aria-controls="FilterCollapse">Filter</button>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="collapse" id="FilterCollapse">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-sm-2"><label>Bulan</label></div>
                                    <div class="col-sm-2"><label>Tahun</label></div>
                                    <div class="col-sm-3"><label>Paket</label></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2"><select name="programFilterBulan" id="programFilterBulan" style="width: 100%;"></select></div>
                                    <div class="col-sm-2"><select name="programFilterTahun" id="programFilterTahun" style="width: 100%;"></select></div>
                                    <div class="col-sm-3"><select name="programFilterPaket" id="programFilterPaket" style="width: 100%;"></select></div>
                                    <div class="col-sm-2"><input type='button' id='programFilterBtnCari' class='btn btn-primary' value='Tampilkan' style='height: 37px;'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="padding-top: 24px;">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-sm table-striped table-bordered" id="table_program_umrah" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Tour Code</th>
                                    <th class="text-center">Pembimbing</th>
                                    <th class="text-center">Tgl. Keberangkatan</th>
                                    <th class="text-center">Tgl. Kepulangan</th>
                                    <th class="text-center">Generate?</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFormV2">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalFormV2_title"></h4>
                    <button type="button" class="close" onclick="closeModal('modalFormV2')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row" style="display: none;">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program Umrah ID</label>
                                <input type="text" id="tourCode_programUmrah_id" name="tourCode_programUmrah_id" class="form-control form-control-sm" style="width: 100%;" placeholder="Program Umrah ID" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tour Code</label>
                                <select name="tourCode_id" id="tourCode_id" class="form-control form-control-sm" style="width: 100%;" onchange="showData('tourCode', this.value)"></select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Keberangkatan</label>
                                <input type="text" name="tourCode_dptDate" id="tourCode_dptDate" class="form-control form-control-sm" readonly style="background: white; cursor: pointer; height: 37.5px;" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Kedatangan</label>
                                <input type="text" name="tourCode_arvDate" id="tourCode_arvDate" class="form-control form-control-sm" readonly style="background: white; cursor: pointer; height: 37.5px;" placeholder="DD/MM/YYYY">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Pembimbing</label>
                                <input type="text" name="tourCode_mentorName" id="tourCode_mentorName" class="form-control form-control-sm" style="height: 37.5px;" placeholder="Nama Pembimbing" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Program</label>
                                <select name="tourCode_programID" id="tourCode_programID" class="form-control form-control-sm" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnDeleteV2" onclick="doDelete(this.id)">Hapus</button>
                    <button type="button" class="btn btn-secondary" id="btnCancelV2" onclick="closeModal('modalFormV2')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanV2" onclick="doSimpanV2('modalFormV2', this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalShowTourCode">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="mdoal-title">Tambah Data Jadwal Umrah</h4>
                    <button class="close" onclick="closeModalTourCode('modalShowTourCode')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Tour Code</label>
                                <input type="text" class="form-control form-control-sm" name="mst_tourCode_id" id="mst_tourCode_id" placeholder="Generate Otomatis" readonly style="height: 38px;">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Program</label>
                                        <select class="form-control form-control-sm" style="width: 100%;" name="mst_tourCode_program" id="mst_tourCode_program"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label>Kapasitas (Orang)</label>
                                        <input type="text" name="mst_tourCode_capacity" id="mst_tourCode_capacity" inputmode="numeric" placeholder="Kapasitas" class="form-control form-control-sm" style="height: 38px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Tgl. Keberangkatan & Tgl. Kepulangan</label>
                                        <input type="text" name="mst_tourCode_date" id="mst_tourCode_date" placeholder="DD/MM/YYYY s/d DD/MM/YYYY" class="form-control form-control-sm" style="height: 38px; background: white; cursor: pointer;" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Pembimbing</label>
                                        <select class="form-control form-control-sm" id="mst_tourCode_mentor" name="mst_tourCode_mentor" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Quad Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost41" id="mst_tourCode_cost41" inputmode="numeric" placeholder="Quad Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Triple Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost31" id="mst_tourCode_cost31" inputmode="numeric" placeholder="Triple Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Double Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost21" id="mst_tourCode_cost21" inputmode="numeric" placeholder="Double Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Lamanya (hari)</label>
                                        <input type="text" name="mst_tourCode_duration" id="mst_tourCode_duration" inputmode="numeric" placeholder="Lamanya" class="form-control form-control-sm" style="height: 38px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Tujuan</label>
                                        <select class="form-control form-control-sm" id="mst_tourCode_destination" name="mst_tourCode_destination" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Rute</label>
                                        <select class="form-control form-control-sm" id="mst_tourCode_route" name="mst_tourCode_route" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Tour Leader</label>
                                        <select class="form-control form-control-sm" id="mst_tourCode_tourLeader" name="mst_tourCode_tourLeader" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Quad Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost42" id="mst_tourCode_cost42" inputmode="numeric" placeholder="Quad Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Triple Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost32" id="mst_tourCode_cost32" inputmode="numeric" placeholder="Triple Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label>Double Cost ($)</label>
                                        <input type="text" name="mst_tourCode_cost22" id="mst_tourCode_cost22" inputmode="numeric" placeholder="Double Cost" class="form-control form-control-sm" style="height: 38px;" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <label>Note</label>
                                <textarea class="form-control form-control-sm" id="mst_tourCode_note" name="mst_tourCode_note" rows="4" placeholder="Keterangan"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModalTourCode('modalShowTourCode')">Batal</button>
                    <button class="btn btn-primary" onclick="simpanProgramV2(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src={{ asset('js/divisi/operasional/program/index.operasional.program.js') }}></script>
@endpush