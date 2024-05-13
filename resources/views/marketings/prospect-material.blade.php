@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/ladda/ladda-themeless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
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
                       <form action="{{route('marketing.prospectmaterial.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <div class="col-sm-2 col-md-2 col-lg-2">
                                <input type="text" name="year" class="form-control form-control-sm" placeholder="Tahun" required>
                            </div>
                            <div class="col-sm-5 col-md-5 col-lg-5">
                                <button type="submit" class="btn btn-sm btn-primary"><i
                                       class="fa fa-download"></i> Generate Alumni Umrah
                               </button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Label</th>
                                        <th>CS</th>
                                        <th>Alumni</th>
                                        <th>Created At</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable">
                                </tbody>
                              
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/loaders.js') }}"></script>
    <script src="{{ asset('js/ladda-button.js') }}"></script>
    <script src="{{ asset('js/marketings/prospect-material.js') }}"></script>

@endpush
