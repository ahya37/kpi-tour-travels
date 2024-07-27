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

        input[type=text] {
            height: 37.5px;
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
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 style="margin: 0px;">RKAP Tahun @php echo date('Y'); @endphp</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 style="margin: 0px;">
                            <span id="act_rkap_loading">
                                <div class="spinner-border"></div>
                            </span>
                            <span id="act_rkap_text" class="d-none"></span>
                        </h2>
                    </div>
                    <a href="#rkap" class="card-footer" onclick="showModal('modal_rkap_finance', '', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 style="margin: 0px;">Aktivitas User</h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 style="margin: 0px;">
                            <span id="act_user_loading"><div class="spinner-border"></div></span>
                            <span id="act_user_text" class="d-none"></span>
                        </h2>
                    </div>
                    <a href="#" class="card-footer" onclick="showModal('modal_daily_activity', '', '')">
                        Tambah Data
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_rkap_finance">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="modal-title" id="modal_rkap_title"></h4>
                    <button class="close" onclick="closeModal('modal_rkap_finance')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12"><h2 style="margin: 0px;">RKAP Finance</h2></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_daily_activity">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="modal-title" id="modal_daily_title"></h4>
                    <button class="close" onclick="closeModal('modal_daily_activity')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2 style="margin: 0px;" id="modal_daily_calendar_title"></h2>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" name="current_date" id="current_date">
                            <div id="calendar" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="btn_cancel" title="Tututp Tampilan" onclick="closeModal('modal_daily_activity')">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_daily_trans">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" class="mt-1" id="modal_daily_trans_title"></h4>
                    <button class="close" onclick="closeModal('modal_daily_trans')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 border-right">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h2>Aktivitas Harian</h2>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group d-none">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Aktivitas ID</label>
                                        <input type="text" class="form-control form-control-sm" name="daily_trans_jenis" id="daily_trans_jenis" placeholder="Aktivitas ID" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Kategori</label>
                                        <select name="daily_trans_category" id="daily_trans_category" class="form-control form-control-sm" style="width: 100%;"></select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Uraian</label>
                                        <input type="text" name="daily_trans_title" id="daily_trans_title" class="form-control form-control-sm" placeholder="Uraian Aktivitas">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Tanggal Aktivitas</label>
                                        <input type="text" name="daily_trans_start_date" id="daily_trans_start_date" class="form-control form-control-sm" placeholder="DD/MM/YYYY" readonly style="background: white; cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row mb-2">
                                    <div class="col-sm-12">
                                        <label>Deskripsi</label>
                                        <textarea name="daily_trans_description" id="daily_trans_description" class="form-control form-control-sm" rows="4" placeholder="Tulis Deskripsi"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div id="v_aktivitas_operasional" class="d-none">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <h2>Aktivitas Operasional</h2>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-group" id="v_trans_code_select">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tour Code</label>
                                            <select name="daily_trans_tour_code" id="daily_trans_tour_code" class="form-control form-control-sm" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group d-none" id="v_trans_code_text">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tour Code</label>
                                            <input type="text" name="daily_trans_tour_code_text" id="daily_trans_tour_code_text" class="form-control form-control-sm" placeholder="Tour Code" readonly>
                                            <input type="hidden" name="daily_trans_pkb_id" id="daily_trans_pkb_id" class="form-control form-control-sm">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" id="v_opr_pkb_id">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Jenis Pekerjaan</label>
                                            <select name="daily_trans_opr_pkb_id" id="daily_trans_opr_pkb_id" class="form-control form-control-sm" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Uraian</label>
                                            <input type="text" name="daily_trans_opr_pkb_title" id="daily_trans_opr_pkb_title" class="form-control form-control-sm" readonly placeholder="Uraian">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row mb-2">
                                        <div class="col-sm-12">
                                            <label>Tanggal Aktivitas</label>
                                            <input type="text" name="daily_trans_opr_pkb_date" id="daily_trans_opr_pkb_date" class="form-control form-control-sm" readonly placeholder="Tgl. Awal s/d Tgl. Akhir">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="btnHapus" title="Hapus Data" onclick="doSimpan('hapus')">Hapus Data</button>
                    <button type="button" class="btn btn-secondary" id="btnBatal" title="Batal Transaksi" onclick="closeModal('modal_daily_trans')">Batal</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan" title="Simpan Data" value="" onclick="doSimpan(this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')s
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/dashboard/index.js') }}"></script>
@endpush