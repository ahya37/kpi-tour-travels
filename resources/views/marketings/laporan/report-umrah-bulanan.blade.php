@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
<link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{asset('assets/css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
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
                                <div class="col-lg-4 m-b-xs">
                                    <div class="form-group row" id="data_5">
                                        <div class="input-daterange " id="datepicker">
                                            <input type="text" class="form-control-sm form-control month-end" name="end" id="month-end" value="" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-1">
                                    <div class="input-group"> <button type="button" class="btn btn-sm btn-primary" id="submitRangeDatePerPic">Go!
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
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <span class="label label-danger float-right" style="background-color: #DF9E0F">Annual</span>
                    </div>
                    <h5>Target</h5>
                </div>
                <div class="ibox-content" id="graph-container-totalTarget">
                    <h1 class="no-margins" id="totalTarget"></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <span class="label label-success float-right" style="background-color: #a3e1d4">Annual</span>
                    </div>
                    <h5>Realisasi</h5>
                </div>
                <div class="ibox-content" id="graph-container-totalRealisasi">
                    <h1 class="no-margins" id="totalRealisasi"></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <span class="label label-success float-right" style="background-color: #828282">Annual</span>
                    </div>
                    <h5>Selisih</h5>
                </div>
                <div class="ibox-content" id="graph-container-totalSelisih">
                    <h1 class="no-margins" id="totalSelisih"></h1>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <span class="label label-success float-right" style="background-color: #C00000">Annual</span>
                    </div>
                    <h5>Persentase <i class="fa fa-bolt text-success"></i></h5>
                </div>
                <div class="ibox-content" id="graph-container-totalPersentage">
                    <h1 class="text-success" id="totalPersentage"></h1>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Pencapaian Per Bulan</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <div id="graph-container-jamaahperbulan">
                        <canvas id="jamaahperbulan" width="100%"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Pencapaian Per Program</h5>
                </div>
                <div class="ibox-content">
                    <div id="graph-container-jamaahperprogram">
                        <canvas id="jamaahperprogram" width="100"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="ibox">
                <div class="ibox-title">
                    <div class="col-md-5 col-sm-5">
                        <h5>Pencapaian Per PIC</h5>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="m-t-sm">
                        <div class="row">
                            <div class="col-md-12">
                               <div class="mt-4" id="graph-container-jamaahperpic">
                                <canvas id="jamaahperpic" width="100"></canvas>
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
                    {{-- <button type="button" class="btn btn-sm btn-primary" onclick="onSingkron(this)"
                        id="{{ $marketingTargetId }}"><i class="fa fa-download"></i> Download PDF
                    </button> --}}
                    <h5>Rincian Per Bulan</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        {{-- {!! $html !!} --}}
                        <table class="table table-striped table-bordered table-hover data">
                            <thead>
                                <tr>
                                    <th class="centered" style="align-item: center" width="2%">No</th>
                                    <th class="centered" width="8%">Bulan</th>
                                    <th class="centered">Program</th>
                                    <th class="centered">Terget</th>
                                    <th class="centered">Realisasi</th>
                                    <th class="centered">Selisih</th>
                                    <th class="centered">Persentase</th>
                                </tr>
                            </thead>
                            @foreach ($res_target as $item)
                            <tr style="background-color: #FFFFFF">
                                <td rowspan="{{ $item['count_list_program'] + 1 }}"
                                    style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px">
                                    <b>{{ $item['nomor_bulan'] }}</b>
                                </td>
                                <td rowspan="{{ $item['count_list_program'] + 1 }}" align="center"
                                    style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px">
                                    <b>{{ $item['bulan'] }}<b>
                                </td>
                            </tr>
                            @php
                            $no = 1;
                            @endphp
                            @foreach ($item['list_program'] as $program)
                            @php
                            // $persentase_per_program =
                            $formatNumber->persentage($program['realisasi'],$program['target']);
                            // if ($persentase_per_program !== null) {
                            // $persentase_per_program = $formatNumber->persen($persentase_per_program);
                            // }
                            @endphp
                            <tr style="background-color: {{ $program['color'] }}">
                                <td>{{ $no++ }} . {{ $program['program'] }}</td>
                                <td style="text-align: right">{{ $program['target']}}</td>
                                <td style="text-align: right">{{ $program['realisasi']}}</td>
                                <td style="text-align: right">{{ $program['selisih']}}</td>
                                <td style="text-align: right">0</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="3" style="text-align: right">Jumlah</th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($item['jml_target']) }}
                                </th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($item['jml_realisasi']) }}
                                </th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($item['jml_selisih']) }}
                                </th>
                                <th style="text-align: right">{{ $item['persentage_jml_pencapaian'] }} %</th>

                            </tr>
                            @endforeach
                            <tr>
                                <th colspan="3" style="text-align: right">Total</th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($total_target) }}</th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($total_realisasi) }}</th>
                                <th style="text-align: right">{{ $formatNumber->decimalFormat($total_selisih) }}</th>
                                <th style="text-align: right">{{ $persentage_total_pencapaian }} %</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('addon-script')
<script src="{{asset('assets/js/plugins/fullcalendar/moment.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="{{asset('assets/js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('assets/js/plugins/select2/select2.full.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/chartJs/Chart.min.js')}}"></script>
<script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/csrf-token.js') }}"></script>
<script src="{{ asset('js/marketings/report-umrah-bulanan.js') }}"></script>
@endpush