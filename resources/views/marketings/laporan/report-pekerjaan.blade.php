@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
<link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">

<style>
    th {
        text-align: left;
    }

    th.centered {
        text-align: center;
        vertical-align: middle;
    }
</style>

@endpush

@section('breadcrumb')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>{{ $title ?? '' }}</h2>
    </div>
</div>
@endsection

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-5">
                            <h5>Filter</h5>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group float-right" id="data_5">
                                <select class="form-control form-control-sm select2_demo_2" id="created_by"
                                    name="created_by">
                                    <option value="">-Pilih Karyawan-</option>
                                    @foreach ($employees as $item)
                                    <option value="{{ $item->user_id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        {{-- <div class="col-md-2">
                            <div class="form-group float-right" id="data_1">
                                <div class="input-group date">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="date" value="">
                                </div>
                            </div>
                        </div> --}}
                        <div class="col-lg-3">
                            <div class="input-group"> 
                                <button type="button" class="btn btn-primary mr-2" id="submitFilter">Go!</button>
                                <button type="button" class="btn btn-primary" id="submitClear">Clear!</button> 
                        </div>
                        </div>
                        {{-- <div class="col-lg-1">
                            <div class="input-group"> <button type="button" class="btn btn-primary" id="submitClear">Clear
                            </button></div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Daftar Aktivitas</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data">
                            <thead >
                                <tr >
                                    <th class="centered" width="8%" style="background-color: #FFFF00">Bulan</th>
                                    <th class="centered" width="8%" style="background-color: #FFFF00">Tanggal</th>
                                    <th class="centered" width="70%" style="background-color: #FFFF00">Aktivitas</th>
                                    <th class="centered" style="background-color: #FFFF00">PIC</th>
                                </tr>
                            </thead>
                            <tbody id="dataBody"></tbody>
                        </table>
                    </div>
                    <div id="divLoading"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('addon-script')
{{-- <script src="{{asset('assets/js/plugins/fullcalendar/moment.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/chartJs/Chart.min.js')}}"></script>
<script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/csrf-token.js') }}"></script> --}}
<script src="{{asset('assets/js/plugins/fullcalendar/moment.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{ asset('js/marketings/laporan/report-pekerjaan.js') }}"></script>
@endpush