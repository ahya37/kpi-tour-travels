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
            height: 38px;
        }

        .text-primary {
            color: #1ab394;
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
            <div class="col-6">
                <h2 class="no-margins">Summary Data</h2>
            </div>
            <div class="col-3">&nbsp;</div>
            <div class="col-3">
                <select name="filter_bulan" id="filter_bulan" style="width: 250px;" class="form-control" onchange="showDataDashboard(this.value)">
                    <option selected disabled>Pilih Bulan</option>
                </select>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-lg-4 col-md-6 col-12 mb-lg-0 mb-2">
                <div class="card">
                    <div class="card-header bg-success">
                        <h4 class="no-margins font-weight-normal">Saldo Awal Bulan <span id="title_bulan_saldo_awal">{{ date('F') }}</span></h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins" id="dashboard_saldo_awal">Rp. 0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-lg-0 mb-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins font-weight-normal">Debit <span id="title_bulan_debit">{{ date('F') }}</span></h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins" id="dashboard_saldo_debit">Rp. 0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-lg-0 mb-2">
                <div class="card">
                    <div class="card-header bg-danger">
                        <h4 class="no-margins font-weight-normal">Kredit <span id="title_bulan_kredit">{{ date('F') }}</span></h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins" id="dashboard_saldo_kredit">Rp. 0.00</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2 class="no-margins">Menu Transaksi</h2>
            </div>
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins card-title">
                            <label class="no-margins font-weight-normal">Pengajuan Keuangan</label>
                        </h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins" id="dashboard_pengajuan_keuangan">
                            <span class="spinner spinner-border"></span>
                        </h2>
                    </div>
                    <div class="card-footer" style="cursor: pointer;" title="Lihat Detail Pembayaran" onclick="showModal('modal_pengajuan_keuangan', 'list', '')">
                        <span class="text-success">Lihat Detail</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins card-title">
                            <label class="no-margins font-weight-normal">Pembayaran Umrah <span id="year_title_pembayaran_umrah">{{ date('Y') }}</span></label>
                        </h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins" id="dashboard_pembayaran_umrah">
                            <span class="spinner spinner-border"></span>
                        </h2>
                    </div>
                    <div class="card-footer" style="cursor: pointer;" title="Pembayaran Umrah">
                        <span class="text-success">Lihat Detail</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins card-title">
                            <label class="no-margins font-weight-normal">Pembayaran Haji <span id="year_title_pembayaran_haji">{{ date('Y') }}</span></label>
                        </h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins" id="dashboard_pembayaran_haji">
                            <span class="spinner spinner-border"></span>
                        </h2>
                    </div>
                    <div class="card-footer" style="cursor: pointer;" title="Pembayaran Haji" onclick="showModal('modal_pembayaran_haji')">
                        <span class="text-success">Lihat Detail</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h2 class="no-margins">Menu Pelaporan</h2>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-md-2">
                <div class="card card-body bg-primary" style="cursor: pointer;" title="Download Report Excel" onclick="showModal('modal_report_pembayaran_haji', '', '')">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-xl-2 col-0 d-xl-block d-none text-center">
                            <i class="fa fa-file"></i>
                        </div>
                        <div class="col-xl-10 col-12 d-block">
                            <h4 class="font-weight-bold no-margins">Pembayaran Haji</h4>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-md-2">
                <div class="card card-body bg-primary" style="cursor: pointer;" title="Perhitungan HPP" onclick="showModal('modal_hitung_hpp', '', '')">
                    <div class="row align-items-center justify-content-between">
                        <div class="col-xl-2 col-0 d-xl-block d-none text-center">
                            <i class="fa fa-dollar"></i>
                        </div>
                        <div class="col-xl-10 col-12 d-block">
                            <h4 class="font-weight-bold no-margins">Perhitungan HPP</h4>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>

    {{-- MODUL TRANSAKSI --}}
    @include('divisi.finance.pembayaran.pengajuan_keuangan.modal_pengajuan_keuangan')
    @include('divisi.finance.pembayaran.pengajuan_keuangan.modal_pengajuan_keuangan_detail')
    @include('divisi.finance.pembayaran.pembayaran_haji.modal_pembayaran_haji')
    @include('divisi.finance.pembayaran.pembayaran_haji.modal_pembayaran_haji_form')

    {{-- REPORT --}}
    @include('divisi.finance.pembayaran.pembayaran_haji.modal_report_pembayaran_haji')
    @include('divisi.finance.pembayaran.perhitungan_hpp.modal_hitung_hpp')

@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/pembayaran/index.js') }}"></script>
@endpush