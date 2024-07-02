@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/ladda/ladda-themeless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
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
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        Form Create Data Group Division
                    </div>
                    <div class="ibox-content">
                        <fieldset>
                            <div class="form-group row mb-2">
                                <div class="col-sm-2 col-form-label">
                                    <span class="pt-1">Name Division</span>
                                </div>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" placeholder="Division Name" autofocus autocomplete="off">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/loaders.js') }}"></script>
    <script src="{{ asset('js/ladda-button.js') }}"></script>
@endpush