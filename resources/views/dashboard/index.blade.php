@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
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
    {{-- <div class="wrapper wrapper-content animated fadeInRight border my-4" style="height: 50vh;">
        <div class="row">

        </div>
    </div> --}}
    <input type="text" id="location_1">
    <input type="text" id="location_2">
    <div class="container-fluid">
        <div class="wrapper wrapper-content">
            <input type="hidden" id="prs_user_id" value="{{ $user_id }}">
            <div class="row">
                <div class="col-sm-3">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="no-margins" id="prs_date">Absensi</h4>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <h1>
                                        <label id="prs_time">00:00:00</label>
                                    </h1>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <button class="btn btn-primary w-100" onclick="showModal('modalShowCamera', 'masuk')">
                                        <i class="fa fa-sign-in"></i> Absen Masuk
                                    </button>
                                </div>
                                <div class="col-sm-6">
                                    <button class="btn btn-danger w-100" onclick="showModal('modalShowCamera', 'keluar')">
                                        <i class="fa fa-sign-out"></i> Absen Pulang
                                    </button>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <div class="d-flex flex-column w-100">
                                        <span id="prs_text_masuk"></span>
                                        <span id="prs_text_keluar"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>     
    </div>

    <div class="modal fade" id="modalShowCamera">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" onclick="closeModal('modalShowCamera')">&times;</button>
                </div>
                <div class="modal-body h-100" id="body_camera">
                    <div class="row">
                        <div class="col-sm-12">
                            <video width="480" height="640" autoplay id="camera"></video>
                            <canvas id="takePhoto" style="width: 100%; height: 100%; border: 1px solid rgba(0, 0, 0, 0.2); padding: 8px;" class="d-none"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center px-2">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" onclick="shutterCamera()" id="btn_takePhoto">
                                <i class="fa fa-camera"></i> Ambil Foto
                            </button>
                            {{-- BUTTON CANCEL AND SAVE --}}
                            <button class="btn btn-danger d-none" id="btn_cancelData" onclick="batalSimpanData()">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button class="btn btn-primary d-none" id="btn_simpanData" value="" onclick="simpanData(this.value)">
                                <i class="fa fa-save"></i> Simpan Data
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-body w-100 d-none" id="body_data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <canvas id="getPhoto" style="width: 100%; height: 100%; border: 1px solid rgba(0, 0, 0, 0.2); padding: 9px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('addon-script')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    {{-- MOMENT --}}
    <script src="{{ asset('js/customJS/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/id.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/moment-timezone-with-data.min.js') }}"></script>
    {{-- SWEETALERT2 --}}
    <script src="{{ asset('js/customJS/SweetAlert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/dashboard/index.dashboard.js') }}"></script>
@endpush
