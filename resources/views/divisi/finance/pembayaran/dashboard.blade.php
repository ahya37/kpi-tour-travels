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
            <div class="col-lg-4 col-md-6 col-12 mb-sm-0 mb-sm-2">
                <div class="card">
                    <div class="card-header bg-success">
                        <h4 class="no-margins font-weight-normal">Saldo Awal Bulan <span id="title_bulan_saldo_awal">{{ date('F') }}</span></h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins" id="dashboard_saldo_awal">Rp. 0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-sm-0 mb-sm-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins font-weight-normal">Debit <span id="title_bulan_debit">{{ date('F') }}</span></h4>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins" id="dashboard_saldo_debit">Rp. 0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-sm-0 mb-sm-2">
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
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-12">
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
                    <a href="#" class="card-footer" title="Lihat Detail Pembayaran" onclick="showModal('modal_pengajuan_keuangan', 'list', '')">
                        Lihat Detail
                    </a>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins card-title">
                            <label class="no-margins font-weight-normal">Pembayaran Jemaah <span id="year_title_pembayaran_jemaah">{{ date('Y') }}</span></label>
                        </h4>
                    </div>
                    <div class="card-body text-right">
                        <h2 class="no-margins" style="padding-top:" id="dashboard_pembayaran_jemaah">
                            <span class="spinner spinner-border"></span>
                            {{-- <span>52</span> --}}
                        </h2>
                    </div>
                    <a href="#" class="card-footer" title="Pembayaran Jemaah" >
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
    </div>

    @include('divisi.finance.pembayaran.pengajuan_keuangan.modal_pengajuan_keuangan')
    @include('divisi.finance.pembayaran.pengajuan_keuangan.modal_pengajuan_keuangan_detail')
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/finance/pembayaran/index.js') }}"></script>
@endpush