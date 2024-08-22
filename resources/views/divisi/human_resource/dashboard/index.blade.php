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

    .menengah { 
        display     : flex;
        align-items : center;
        justify-content: center;
    }
    ul {
        margin: 0px;
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
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    {{-- <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script> --}}
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    {{-- MOMENT AREA --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.36/moment-timezone-with-data.min.js"></script> --}}
    {{-- NPROGRESS --}}
    {{-- <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script> --}}
    <script src="{{ asset('js/divisi/operasional/index.operasional.js') }}"></script>
@endpush