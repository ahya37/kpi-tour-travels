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
            <div class="col-sm-6 col-12">
                <div class="card">
                    <div class="card-header d-flex flex-row justify-content-between align-items-center">
                        <h4 class="no-margins fw-bold">Rekapitulasi Data</h4>
                        <button class="btn btn-sm btn-primary" title="Tarik Data" onclick="doTarikData('table_summary_data')"><i class="fa fa-redo"></i> Tarik Data</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped" style="width: 100%;" id="table_summary_data">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">Jemaah</th>
                                        <th class="text-center align-middle">Perjalanan</th>
                                        <th class="text-center align-middle">Agen</th>
                                        <th class="text-center align-middle">Pembimbing</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-left align-middle" colspan="4" id="table_summary_data_footer">Update Terakhir</th>
                                    </tr>
                                </tfoot>
                            </table>
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
    <script type="text/javascript" src="{{ asset('js/activities/tarik_data/perciktourscom/index.js') }}"></script>
@endpush