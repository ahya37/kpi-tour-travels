@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/percik_fullcalendar.css') }}">
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
            <div class="col-sm-12">
                <div class="card shadow mb-4">
                    <div class="card-header">
                        <h3 style="margin:0px;" id="card_title">List Aktivitas Harian <span id="kalender_bulan"></span></h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div id="calendar_loading">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="height: 650px;">
                                        <div class="spinner-border"></div>
                                        <label><b>Data Sedang Dimuat</b></label>
                                    </div>
                                </div>
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_form">
        <div class="modal-dialog modal-dialog-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 style="margin: 0px;" id="modal_form_title" class="modal-title"></h4>
                    <button class="close" onclick="closeModal('modal_form')">&times;</button>
                </div>
                <div class="modal-body">
                    
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/divisi/digital/index.digital.js') }}"></script>
@endpush