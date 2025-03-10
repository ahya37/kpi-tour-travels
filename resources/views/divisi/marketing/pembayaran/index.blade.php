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
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins">
                            <label class="no-margins font-weight-bold">Table Pembayaran Haji</label>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-3 col-sm-6 col-12">
                                <div class="form-group">
                                    <label for="select_filter_keberangkatan">Filter Keberangkatan</label>
                                    <select id="select_filter_keberangkatan" style="width: 100%;" class="form-control select2"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary btn-sm" onclick="showModal('modal_pembayaran_haji')"><i class="fa fa-plus"></i> Tambah Data</button>
                                <button class="btn btn-primary btn-sm"><i class="fa fa-file-excel"></i> Download Report</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered" style="width: 100%;" id="table_pembayaran_haji">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle" style="width: 8%;">No</th>
                                                <th class="text-center align-middle">Nama</th>
                                                <th class="text-center align-middle" style="width: 15%;">Paket</th>
                                                <th class="text-center align-middle" style="width: 15%;">Keberangkatan</th>
                                                <th class="text-center align-middle" style="width: 13%;">Status Bayar</th>
                                                <th class="text-center align-middle" style="width: 10%;">Aksi</th>
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
    </div>

    {{-- SHOW MODAL PEMBAYARAN HAJI --}}
    @include('divisi.marketing.pembayaran.modal_pembayaran_haji')
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/marketing/index.marketing.js') }}"></script>
@endpush