@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/yearpicker/yearpicker.css') }}" rel="stylesheet">

    <style>
    label {
        font-weight: bold;
    }

    .menengah { 
        display     : flex;
        align-items : center;
        justify-content: center;
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }

    button:disabled {
        cursor: not-allowed;
        pointer-events: all !important;
    }

    input[type="text"]:read-only {
        background-color: white;
        cursor: pointer;
    }

    input[type="text"]:disabled {
        background-color: #e9ecef;
        border-color: #ccc;
        cursor: no-drop;
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
    <div class="container-fluid">
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row align-items-center">
                <div class="col-xl-3 col-lg-6">
                    <div class="widget style1 navy-bg" title="Lakukan Simulasi Perhitungan Agent" onclick="showModal('modal_simulasi', '', 'view')" style="cursor: pointer;">
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <i class="fa fa-calculator fa-3x"></i>
                            <h3 class="font-bold no-margins">Simulasi</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-6">
                    <div class="widget style1 navy-bg" title="Pengaturan Agent" onclick="showModal('modal_pengaturan_agen', '', 'view')" style="cursor: pointer;">
                        <div class="d-flex flex-row align-items-center justify-content-between">
                            <i class="fa fa-cog fa-3x"></i>
                            <h3 class="font-bold no-margins">Pengaturan Agent</h3> 
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4>
                            <label class="no-margins font-weight-bold">List Agent</label>
                            </h4>
                        </div>
                        <div class="text-right">
                            @if ($user_roles == 'admin')
                                <button class="btn btn-primary" title="Ambil Data Umhaj" onclick="showModal('modal_tarik_data_agent', '', '')">Tarik Data</button>
                            @endif
                            <button class="btn btn-primary" title="Tambah Data" onclick="showModal('modal_data_agent', '', 'add')">Tambah Data</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm datatable" id="table_list_agent">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%">No</th>
                                            <th class="text-center align-middle" style="width: 10%">Kode MKK</th>
                                            <th class="text-center align-middle" style="width: 8%">Kode Akun</th>
                                            <th class="text-center align-middle" style="width: 30%">Nama</th>
                                            <th class="text-center align-middle" style="width: 30%">PIC</th>
                                            <th class="text-center align-middle">Kontak</th>
                                            <th class="text-center align-middle" style="width: 5%">Aksi</th>
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

    

    <div class="modal fade" id="modal_simulasi">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="modal-title no-margins"><label class="no-margins">Simulasi Perhitungan Point Agent</label></h4>
                    <button class="close" onclick="closeModal('modal_simulasi')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row text-center">
                        <div class="col-sm-12">
                            <h1 class="no-margins">Simulasi Perhitungan Point Agent</h1>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-4 align-items-top">
                        <div class="col-sm-6 text-center border-right mb-2">
                            <h2 class="no-margins"><label class="font-weight-light no-margins">Total Pengumpulan Point</label></h2>
                            <hr>
                            <div class="row ml-2 mr-2 text-left border">
                                <div class="col-sm-4">&nbsp;</div>
                                <div class="col-sm-2 text-center"><label class="no-margins">Point</label></div>
                                <div class="col-sm-2 text-center"><label class="no-margins">Bonus</label></div>
                                <div class="col-sm-2 text-center"><label class="no-margins">PU</label></div>
                                <div class="col-sm-2 text-center"><label class="no-margins">PA</label></div>
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Tahun Pertama</label>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="point_1">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="bonus_1">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="total_1">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="sisa_1">0</span>
                                </div>
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Tahun Kedua</label>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="point_2">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="bonus_2">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="total_2">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="sisa_2">0</span>
                                </div>
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Tahun Ketiga</label>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="point_3">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="bonus_3">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="total_3">0</span>
                                </div>
                                <div class="col-sm-2 text-center">
                                    <span id="sisa_3">0</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 text-center mb-2">
                            <h2 class="no-margins"><label class="font-weight-light no-margins">Reward / Hadiah</label></h2>
                            <hr>
                            <div class="row" style="height: 69%;" id="card_umrah_reward">
                                <div class="col-sm-12">
                                    <div class="card card-body bg-primary align-items-center" style="height: 100%;">
                                        <div class="row m-auto">
                                            <div class="col-sm-12">
                                                <h1 class="no-margins">
                                                    <label class="no-margins font-weight-light">
                                                        Selamat, Anda Mendapatkan <span id="total_reward" class="font-weight-bold">0</span> Tiket Umrah
                                                    </label>
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="row">
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-light">Pendapatan Tahun Pertama</h2>
                                </div>
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-light">Pendapatan Tahun Kedua</h2>
                                </div>
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-light">Pendapatan Tahun Ketiga</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-bold" id="total_pendapatan_tahun_pertama">
                                        Rp. 0,00
                                    </h2>
                                </div>
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-bold" id="total_pendapatan_tahun_kedua">
                                        Rp. 0,00
                                    </h2>
                                </div>
                                <div class="col-sm-4">
                                    <h2 class="no-margins font-weight-bold" id="total_pendapatan_tahun_ketiga">
                                        Rp. 0,00
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" id="btn_table_simulasi" value="0" onclick="addColumnTable('table_simulasi', this.value, [])">Tambah Data</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hovered table-bordered" style="width: 100%;" id="table_simulasi">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">Aksi</th>
                                            <th class="text-center align-middle">Tahun</th>
                                            <th class="text-center align-middle">Jan</th>
                                            <th class="text-center align-middle">Feb</th>
                                            <th class="text-center align-middle">Mar</th>
                                            <th class="text-center align-middle">Apr</th>
                                            <th class="text-center align-middle">Mei</th>
                                            <th class="text-center align-middle">Jun</th>
                                            <th class="text-center align-middle">Jul</th>
                                            <th class="text-center align-middle">Agt</th>
                                            <th class="text-center align-middle">Sep</th>
                                            <th class="text-center align-middle">Okt</th>
                                            <th class="text-center align-middle">Nov</th>
                                            <th class="text-center align-middle">Des</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th class="text-right align-middle" id="total_Jan">0</th>
                                            <th class="text-right align-middle" id="total_Feb">0</th>
                                            <th class="text-right align-middle" id="total_Mar">0</th>
                                            <th class="text-right align-middle" id="total_Apr">0</th>
                                            <th class="text-right align-middle" id="total_Mei">0</th>
                                            <th class="text-right align-middle" id="total_Jun">0</th>
                                            <th class="text-right align-middle" id="total_Jul">0</th>
                                            <th class="text-right align-middle" id="total_Agt">0</th>
                                            <th class="text-right align-middle" id="total_Sep">0</th>
                                            <th class="text-right align-middle" id="total_Okt">0</th>
                                            <th class="text-right align-middle" id="total_Nov">0</th>
                                            <th class="text-right align-middle" id="total_Des">0</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_data_agent">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="modal-title font-weight-bold no-margins" id="modal_data_agent_title"></h4>
                    <button class="close" onclick="closeModal('modal_data_agent')" title="Tutup Tampilan">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Kode MKK</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" id="agt_mkk_code" name="agt_mkk_code" placeholder="Kode MKK" onkeyup="uppercase(this.id, this.value), removeInvalid(this.id)" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Nama</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="hidden" id="agt_id" name="agt_id">
                            <input type="text" class="form-control form-control-sm" id="agt_name" name="agt_name" placeholder="Nama Agent" onkeyup="uppercase(this.id, this.value), removeInvalid(this.id)" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">PIC</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="agt_pic" id="agt_pic" placeholder="PIC" onkeyup="uppercase(this.id, this.value)" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-top">
                        <div class="col-sm-3">
                            <label class="no-margins">Alamat</label>
                        </div>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" name="agt_address" id="agt_address" rows="4" style="resize: none;" placeholder="Isi Alamat Lengkap" onkeyup="removeInvalid(this.id)"></textarea>
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Telepon</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="agt_contact_1" id="agt_contact_1" placeholder="Nomor Telepon" inputmode="numeric">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Fax</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="agt_fax" id="agt_fax" placeholder="Fax" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Handphone</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="agt_contact_2" id="agt_contact_2" placeholder="Nomor Handphone" inputmode="numeric" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-center">
                        <div class="col-sm-3">
                            <label class="no-margins">Alamat Email</label>
                        </div>
                        <div class="col-sm-9">
                            <input type="email" class="form-control form-control-sm" name="agt_email" id="agt_email" placeholder="contoh@example.com" autocomplete="off">
                        </div>
                    </div>
                    <div class="row mb-2 align-items-top">
                        <div class="col-sm-3">
                            <label class="no-margins">Note</label>
                        </div>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" name="agt_note" id="agt_note" placeholder="Keterangan Tambahan" rows="4" style="resize: none;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" id="modal_data_agent_batal" onclick="closeModal('modal_data_agent')" title="Tutup Tampilan">Batal</button>
                    <button class="btn btn-primary" id="modal_data_agent_simpan" title="Simpan Data" onclick="doSimpanData('modal_data_agent', this.value, '')">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_pengaturan_agen">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="no-margins font-weight-bold">
                        <i class="fa fa-cog"></i> Pengaturan Agen
                    </h4>
                    <button class="close" title="tutup tampilan" onclick="closeModal('modal_pengaturan_agen')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label class="font-weight-bold no-margins">Pilih Agen</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <select class="form-control form-control-sm" name="sl_agt_id" id="sl_agt_id" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered table-hover" id="table_pengaturan_agen" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">No</th>
                                    <th class="text-center align-middle">Tanggal</th>
                                    <th class="text-center align-middle">Tour Code</th>
                                    <th class="text-center align-middle">Banyaknya</th>
                                    <th class="text-center align-middle">Jenis</th>
                                    <th class="text-center align-middle">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div class="modal-footer align-items-center">
                    <button class="btn btn-secondary" id="btn_close_modal_pengaturan_agen" title="Tutup Tampilan" onclick="closeModal('modal_pengaturan_agen')">Tutup</button>
                    <button class="btn btn-success" id="btn_tambah_data_modal_pengaturan_agen" title="Tambah Baris" value="0" disabled onclick="addColumnTable('table_pengaturan_agen', this.value, '')">Tambah Baris</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/agent/index.dashboard.js') }}"></script>
@endpush