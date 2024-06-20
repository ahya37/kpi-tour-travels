@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <style>
        th {
        text-align: left; /* Mengatur teks di header tabel menjadi rata kiri */
        }

        th.centered {
            text-align: center; /* Mengatur teks di header tabel menjadi rata tengah */
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
                        <button type="button" class="btn btn-sm btn-primary" onclick="onSingkron(this)" id="{{ $marketingTargetId }}"><i
                                class="fa fa-download"></i> Download PDF
                        </button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            {{-- {!! $html !!} --}}
                            <table class="table table-striped table-bordered table-hover data">
                                <thead>
                                    <tr>
                                        <th  class="centered" style="align-item: center" width="2%">No</th>
                                        <th  class="centered" width="8%">Bulan</th>
                                        <th  class="centered">Program</th>
                                        <th  class="centered">Terget</th>
                                        <th  class="centered">Realisasi</th>
                                        <th  class="centered">Selisih</th>
                                        <th  class="centered">Persentase</th>
                                    </tr>
                                </thead>
                                    @foreach ($res_target as $item)
                                        <tr style="background-color: #FFFFFF">
                                            <td rowspan="{{ $item['count_list_program'] + 1 }}" style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px"><b>{{ $item['nomor_bulan'] }}</b></td>
                                            <td rowspan="{{ $item['count_list_program'] + 1 }}" align="center" style=" display: table-cell; vertical-align: middle;text-align: center;font-size:14px"><b>{{ $item['bulan'] }}<b></td>
                                        </tr>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach ($item['list_program'] as $program)
                                        @php
                                            $persentase_per_program = $formatNumber->persentage($program['realisasi'],$program['target']);
                                            if ($persentase_per_program !== null) {
                                                $persentase_per_program  = $formatNumber->persen($persentase_per_program);  
                                            }
                                        @endphp  
                                            <tr style="background-color: {{ $program['color'] }}">
                                                <td>{{ $no++ }} . {{ $program['program'] }}</td>
                                                <td style="text-align: right">{{ $program['target']}}</td>
                                                <td style="text-align: right">{{ $program['realisasi']}}</td>
                                                <td style="text-align: right">{{ $program['selisih']}}</td>
                                                <td style="text-align: right">{{ $persentase_per_program }} %</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="3" style="text-align: right">Jumlah</th>
                                            <th style="text-align: right">{{ $item['jml_target'] }}</th>
                                            <th style="text-align: right">{{ $item['jml_realisasi'] }}</th>
                                            <th style="text-align: right">{{ $item['jml_selisih'] }}</th>
                                            <th style="text-align: right">{{ $item['persentage_jml_pencapaian'] }} %</th>

                                        </tr>
                                    @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('prepend-script')
    @include('layouts.modals.modal-detail-marketing-targets')
@endpush
@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/marketings/modal-add-detail-marketing-targets.js') }}"></script>
@endpush
