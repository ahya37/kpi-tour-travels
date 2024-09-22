@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
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
                        <h4 class="no-margins">List Absen dari presensi.perciktours.com</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-3">
                                <label class="font-weight-bold">Tanggal Cari</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">
                                <input type="text" class="form-control" placeholder="DD/MM/YYYY" readonly style="background: white; cursor: pointer;" name="tgl_cari" id="tgl_cari">
                            </div>
                            <div class="col-sm-4">
                                <button class="btn btn-primary" onclick="cariData('table_group_division')">Cari</button>
                                <button class="btn btn-primary" onclick="cariData('table_group_division_tarik_data')">Tarik Data</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover table-bordered" id="table_group_division" style="width: 100%;">
                                        <thead>
                                            <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Jam Masuk</th>
                                            <th>Lokasi Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Lokasi Keluar</th>
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
        </div>
    </div>

    
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/activities/tarik_data/absensi/index.tarik_data.absensi.js') }}"></script>
@endpush