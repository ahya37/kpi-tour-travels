@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
<link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
<link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">

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
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal"
                        data-target="#myModal5"><i class="fa fa-plus"></i> Tambah Target Per Bulan
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="onImportTargetUmrahFromUmhaj(this)"
                        id="{{ $marketingTargetId }}"><i class="fa fa-plus"></i> Import Target Dari Umhaj
                    </button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="onSingkron(this)"
                        id="{{ $marketingTargetId }}" ><i class="fa fa-download"></i> Singkronkan Realisasi
                    </button>
                </div>
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover data">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Bulan</th>
                                    <th>Program</th>
                                    <th>Target</th>
                                    <th>Realisasi</th>
                                    <th>Selisih</th>
                                    <th>Opsi</th>
                                </tr>
                            </thead>
                            <tbody id="dataTable"></tbody>
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
<script src="{{ asset('js/marketings/index-detail-targets.js') }}"></script>
@endpush