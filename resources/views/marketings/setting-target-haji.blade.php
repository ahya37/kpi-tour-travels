@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">

    <link href="{{ asset('assets/css/plugins/ladda/ladda-themeless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    {{-- <link href="{{ asset('assets/css/plugins/datapicker/datepicker3.csss') }}" rel="stylesheet"> --}}
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
                @include('layouts.notification')
                <div class="ibox ">
                    <div class="ibox-title">
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#myModal5"><i
                            class="fa fa-plus"></i> Tambah Target
                    </button>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun</th>
                                        <th>Target</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                {{-- <tbody id="dataTable">
                                    @foreach ($prospectMaterial as $item)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $item->cs }}</td>
                                            <td>{{ $item->label }}</td>
                                            <td>{{ $item->members }}</td>
                                            <td>{{ $item->created_at }}</td>
                                            <td></td>
                                        </tr>
                                    @endforeach
                                </tbody> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal inmodal fade" id="myModal5">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title">Tambah Target Haji</h4>
                    </div>
                    <div class="modal-body">
                        <div id="loading"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-white" data-dismiss="modal"
                            onclick="closeModal()">Batal</button>
                        <button type="button" class="btn btn-sm btn-primary ladda-button" id="saveButton">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/datapicker/bootstrap-datepicker.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>

    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/loaders.js') }}"></script>
    <script src="{{ asset('js/ladda-button.js') }}"></script>
    <script src="{{ asset('js/initial-select2.js') }}"></script>
    <script src="{{ asset('js/marketings/setting-target-haji.js') }}"></script>
@endpush