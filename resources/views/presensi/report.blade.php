@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    {{-- <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/ladda/ladda-themeless.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet"> --}}
    <link href="{{asset('assets/css/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/plugins/daterangepicker/daterangepicker-bs3.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">

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
                    <div class="ibox-content">
                       <div class="row">
                            <div class="col-lg-2">
                                <h4>Buat Laporan</h4>
                            </div>
                            <div class="col-lg-3">
                                <div class="input-group">
                                    <input class="form-control form-control-sm  filter-sumber-pendaftaran-umrah" type="text" name="filter-sumber-pendaftaran-umrah" placeholder="Filter tanggal"/> 
                                </div>
                            </div>
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('prepend-script')
    @include('layouts.modals.modal-targets')
@endpush

@push('addon-script')
<script src="{{asset('assets/js/plugins/fullcalendar/moment.min.js')}}"></script>
<script src="{{asset('assets/js/plugins/daterangepicker/daterangepicker.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
<script>
    window.appConfig = {
        appUrl: '{{ env("API_PERCIK_KEY") }}'
    };
</script>
<script src="{{ asset('js/presensi/report.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/spin.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/ladda/ladda.jquery.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/loaders.js') }}"></script>
    <script src="{{ asset('js/marketings/index-targets.js') }}"></script>
    <script src="{{ asset('js/marketings/modal-add-targets.js') }}"></script>
    <script src="{{ asset('js/ladda-button.js') }}"></script> --}}
@endpush
