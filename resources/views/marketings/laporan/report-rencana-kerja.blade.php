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
            <div class="ibox">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-5 col-sm-5">
                            <h5>Filter (Monthly)</h5>
                        </div>
                        <div class="col-md-7 col-sm-7">
                            <div class="row">
                                <div class="col-lg-4 m-b-xs">
                                    <div class="form-group" id="data_5">
                                        <div class="input-daterange " id="datepicker">
                                            <input type="text" class="form-control-sm form-control month-start" id="month-start" name="start" value=""/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <div class="input-group"> <button type="button" class="btn btn-sm btn-primary" id="submitFilter">Go!
                                    </button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5 id="title"></h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data">
                            <thead >
                                <tr >
                                    <th class="centered" width="8%" style="background-color: #F5B487">Tanggal Perencanaan</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Tugas</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Jenis Pekerjaan</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487">Target / Sasaran</th>
                                    <th class="centered" style="background-color: #F5B487">Hasil</th>
                                    <th class="centered" style="background-color: #F5B487">Evaluasi</th>
                                    {{-- <th class="centered" style="background-color: #F5B487">Rincian Kegiatan</th> --}}
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

<!-- Modal -->
<div id="myModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Contoh Modal</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover data">
                    <thead >
                        <tr >
                            <th class="centered" width="8%" style="background-color: #F5B487">No.</th>
                            <th class="centered" width="8%" style="background-color: #F5B487">Tanggal Pelaksanaan</th>
                            <th class="centered" width="25%" style="background-color: #F5B487">Kegiatan</th>
                            <th class="centered" style="background-color: #F5B487">Pelaksana</th>
                        </tr>
                    </thead>
                    <tbody id="dataModalBody"></tbody>
                </table>
            </div>
            <div id="divLoadingModal"></div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
<script src="{{ asset('js/marketings/laporan/report-rencana-kerja.js') }}"></script>
@endpush