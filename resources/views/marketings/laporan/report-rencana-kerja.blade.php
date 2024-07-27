@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
<link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">

{{-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script> --}}

<style>
    th {
        text-align: left;
    }

    th.centered {
        text-align: center;
        vertical-align: middle;
    }
</style>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>
<style>
    .text-right {
        text-align: right;
        width: 5%
    }
    .text-center {
        text-align: center;
        width: 1%
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
                        <div class="col-md-3 col-sm-5">
                            <h5>Pilih Sasaran</h5>
                        </div>
                        <div class="col-md-3 col-sm-7">
                            <div class="form-group">
                                <div class="input-daterange " id="datepicker">
                                    <input type="number" class="form-control-sm form-control year-start"
                                        id="year-start" name="start" value="" required placeholder="Pilih Tahun"/>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-7">
                            <div class="form-group">
                                <select class="form-control form-control-sm sasaran" required></select>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-7">
                            <div class="form-group">
                                <button type="button" class="btn btn-sm btn-primary" id="goFilter">Go</button>
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
                   <h5>Evaluasi Sasaran Umum Umrah</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover " id="datatableEvaluasiSasaranUmum">
                            <thead>
                                <tr>
                                    <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Bulan</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Pencapaian Program</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487" id="pencapaian">Realisasi</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487" id="target">Target</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487">Selisih</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487">Persentase Umrah</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div id="divLoading"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-7 col-sm-5">
                            <h5>Filter (Monthly)</h5>
                        </div>
                        <div class="col-md-5 col-sm-7">
                            <div class="row">
                                <div class="col-lg-4 m-b-xs">
                                    <div class="form-group" id="data_5">
                                        <div class="input-daterange " id="datepicker">
                                            <input type="text" class="form-control-sm form-control month-start"
                                                id="month-start" name="start" value="" placeholder="Pilih Bulan" readonly/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 m-b-xs">
                                    <div class="form-group" id="data_5">
                                        <div class="input-daterange " id="datepickerWeek">
                                            <input type="number" class="form-control-sm form-control week-start"
                                                id="weekPicker" name="week" placeholder="Pilih Minggu"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group"> <button type="button" class="btn btn-sm btn-primary"
                                            id="submitFilter">Go
                                        </button></div>
                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group"> <button type="button" class="btn btn-sm btn-primary"
                                            id="submitClear">Clear
                                        </button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 id="title"></h5>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="lihatPerMinggu" class="btn btn-sm btn-primary d-none" data-toggle="modal" data-target="#myModalPerMinggu" onclick="onLihatPerMinggu(this)">Lihat Per Minggu</button>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover " id="datatable">
                            <thead>
                                <tr>
                                    <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Program</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487" id="pencapaianProgram">Pencapaian</th>
                                    <th class="centered" style="background-color: #F5B487" id="hasilProgram">Realisasi</th>
                                    <th class="centered" width="15%" style="background-color: #F5B487" id="targetProgram">Target</th>
                                </tr>
                            </thead>
                            <tbody id="dataBody"></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah</th>
                                    <th id="totalPersentasePerbulan">0</th>
                                    <th id="totalHasilPerbulan">0</th>
                                    <th id="totalTargetPerbulan">0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="divLoading"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Per minggu --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-10">
                            <h5 id="titledataRincianPerminggu"></h5>
                        </div>
                        <div class="col-md-2">
                            <button type="button" id="lihatPerMinggu" class="btn btn-sm btn-primary d-none" data-toggle="modal" data-target="#myModalPerMinggu" onclick="onLihatPerMinggu(this)">Lihat Per Minggu</button>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="dataRincianPerminggu">
                            <thead>
                                <tr>
                                    <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                    <th class="centered" width="25%" style="background-color: #F5B487">Program</th>
                                    <th class="centered" style="background-color: #F5B487">Minggu ke 1</th>
                                    <th class="centered" style="background-color: #F5B487">Minggu ke 2</th>
                                    <th class="centered" style="background-color: #F5B487">Minggu ke 3</th>
                                    <th class="centered" style="background-color: #F5B487">Minggu ke 4</th>
                                    <th class="centered" style="background-color: #F5B487">Minggu ke 5</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah</th>
                                    <th id="jml_minggu_1"></th>
                                    <th id="jml_minggu_2"></th>
                                    <th id="jml_minggu_3"></th>
                                    <th id="jml_minggu_4"></th>
                                    <th id="jml_minggu_5"></th>
                                </tr>
                            </tfoot>
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
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleRincian"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="dataRincian">
                        <thead>
                            <tr>
                                <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                <th class="centered" width="8%" style="background-color: #F5B487">Tanggal Pelaksanaan
                                </th>
                                <th class="centered" width="25%" style="background-color: #F5B487">Kegiatan</th>
                                <th class="centered" style="background-color: #F5B487">Pelaksana</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


<div id="myModalRincianKegiatan" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleRincianPerMinggu"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="datamyModalRincianKegiatan">
                        <thead>
                            <tr>
                                <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                <th class="centered" style="background-color: #F5B487">Tanggal</th>
                                <th class="centered" width="25%" style="background-color: #F5B487">Program</th>
                                <th class="centered" style="background-color: #F5B487">PIC</th>
                            </tr>
                        </thead>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="myModalJenisPekerjaan" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleJenisPekerjaan"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableShowJenisPekerjaan">
                        <thead>
                            <tr>
                                <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                <th class="centered" width="25%" style="background-color: #F5B487">Jenis Pekerjaan</th>
                                <th class="centered" width="5%" style="background-color: #F5B487">Target</th>
                                <th class="centered" width="5%" style="background-color: #F5B487">Hasil</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div id="myModalAktivitasHarian" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleAktivitasHarian"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="tableShowAktivitasHarian">
                        <thead>
                            <tr>
                                <th class="centered" width="1%" style="background-color: #F5B487">No</th>
                                <th class="centered" width="25%" style="background-color: #F5B487">Tanggal</th>
                                <th class="centered" style="background-color: #F5B487">Kegiatan</th>
                                <th class="centered" style="background-color: #F5B487">Pelaksana</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('addon-script')

<script src="{{asset('assets/js/plugins/fullcalendar/moment.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
{{-- <script src="{{ asset('js/marketings/laporan/report-rencana-kerja.js') }}"></script> --}}
<script src="{{ asset('js/marketings/laporan/report-rencana-kerja-new.js') }}"></script>
@endpush