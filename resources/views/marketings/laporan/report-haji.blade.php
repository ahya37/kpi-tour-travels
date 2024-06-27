@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
<link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
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
            <div class="ibox ">
                <div class="ibox-title">
                    <div class="row">
                        <div class="col-md-5">
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control tahunHaji" id="tahunHaji" name="created_by">
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <button type="button" class="btn btn-primary mr-2" id="submitFilter">Go!</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox-content">
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
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Pencapaian Per PIC</h5>
                </div>
                <div class="ibox-content">
                    <div class="text-center">
                        <div id="graph-container-jamaahperpic">
                            <canvas id="jamaahperpic" width="100%"></canvas>
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
                    <h5>Rincian Per Bulan</h5>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data">
                            <thead>
                                <tr>
                                    <th class="centered"  style="background-color: #F5B487" width="2%">No</th>
                                    <th class="centered" style="background-color: #F5B487">Bulan</th>
                                    <th class="centered" style="background-color: #F5B487">Target</th>
                                    {{-- <th class="centered">Program</th> --}}
                                    <th class="centered" style="background-color: #F5B487">Realisasi</th>
                                    <th class="centered"  style="background-color: #F5B487">Selisih</th>
                                    <th class="centered" style="background-color: #F5B487">Persentase</th>
                                </tr>
                            </thead>
                            <tbody id="dataBody"></tbody>
                            <tfoot id="dataFooter">
                            </tfoot>
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
<script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('js/csrf-token.js') }}"></script>
<script src="{{ asset('js/marketings/laporan/report-haji.js') }}"></script>
<script>
    const percikUrl = "{{ env('API_PERCIK') }}";
    const percikKey = "{{ env('API_PERCIK_KEY') }}";
</script>
@endpush