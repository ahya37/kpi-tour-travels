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
                <div class="col-sm-3 mb-2">
                    <div class="card" id="card_agent">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins font-weight-bold">
                                <label class="no-margins">List Agent</label>
                            </h4>
                        </div>
                        <div class="card-body text-right">
                            <h2 class="no-margins font-weight-normal" id="agent_text">
                                <div class="spinner-border"></div>
                            </h2>
                        </div>
                        <a href="#show_modal_agent" class="card-footer" title="Lihat Detail" onclick="showModal('modal_agent', '', 'view')">
                            Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="col-sm-3 mb-2">
                    <div class="card" id="card_simulasi">
                        <div class="card-header bg-primary">
                            <h4 class="no-margins font-weight-bold">
                                <label class="no-margins">Simulasi Point Agent</label>
                            </h4>
                        </div>
                        <div class="card-body text-center">
                            <h2 class="no-margins font-weight-normal" id="simulasi_text">Lakukan Simulasi</h2>
                        </div>
                        <a href="#show_modal_simulasi" class="card-footer" title="Lihat Detail" onclick="showModal('modal_simulasi', '', 'view')">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_agent">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <h4 class="no-margins font-weight-bold">List Agent</h4>
                    <button class="close" onclick="closeModal('modal_agent')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" title="Perbarui Data" onclick="showModal('modal_tarik_data_agent')">
                                <i class="fa fa-redo"></i> Tarik Data
                            </button>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hovered" id="table_list_agent" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle">No</th>
                                            <th class="text-center align-middle">Nama</th>
                                            <th class="text-center align-middle">PIC</th>
                                            <th class="text-center align-middle">Kontak</th>
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
                        <div class="col-sm-6 text-center border-right">
                            <h2 class="no-margins"><label class="font-weight-light no-margins">Total Pengumpulan Point</label></h2>
                            <hr>
                            <div class="row ml-2 mr-2 text-left border">
                                <div class="col-sm-4">&nbsp;</div>
                                <div class="col-sm-2 text-center"><label class="no-margins">Point</label></div>
                                <div class="col-sm-2 text-center"><label class="no-margins">Bonus</label></div>
                                <div class="col-sm-2 text-center"><label class="no-margins">Total</label></div>
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Point Tahun Pertama</label>
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
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Point Tahun Kedua</label>
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
                            </div>
                            <div class="row ml-2 mr-2 text-left border align-items-center">
                                <div class="col-sm-4">
                                    <label class="mt-2">Point Tahun Ketiga</label>
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
                            </div>
                        </div>
                        <div class="col-sm-6 text-center">
                            <h2 class="no-margins"><label class="font-weight-light no-margins">Reward / Hadiah</label></h2>
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
                            <div class="table-repsonsive">
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
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/agent/index.dashboard.js') }}"></script>
@endpush