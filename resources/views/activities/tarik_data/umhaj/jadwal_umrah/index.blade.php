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
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between">
                        <h4 class="no-margins">
                            <label class="no-margins font-weight-bold">List Jadwal Umrah</label>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
                                <div class="form-group">
                                    <label for="filter_tahun_umhaj">Pilih Tahun</label>
                                    <select name="filter_tahun_umhaj" id="filter_tahun_umhaj" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, this.value)">
                                        <option selected disabled>Pilih Tahun</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="table_jadwal_umrah">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle">No</th>
                                                <th class="text-center align-middle">Tour Code</th>
                                                <th class="text-center align-middle">Tour Leader</th>
                                                <th class="text-center align-middle">Keberangkatan</th>
                                                <th class="text-center align-middle">Kepulangan</th>
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
    <script src="{{ asset('js/activities/tarik_data/umhaj/jadwal_umrah/index.js') }}"></script>
@endpush