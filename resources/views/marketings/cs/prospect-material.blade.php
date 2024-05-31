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
                @include('layouts.notification')
                <div class="ibox ">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover data">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>CS</th>
                                        <th>Label</th>
                                        <th>Alumni</th>
                                        <th>Respon</th>
                                        <th>Tidak Respon</th>
                                        <th>Created At</th>
                                        <th>Opsi</th>
                                    </tr>
                                </thead>
                                <tbody id="dataTable">
                                    @foreach ($prospectMaterials as $item)
                                        <tr>
                                            <td>{{$no++}}</td>
                                            <td>{{$item->cs}}</td>
                                            <td>{{$item->label}}</td>
                                            <td>{{$item->members}}</td>
                                            <td>{{$item->yes_respone}}</td>
                                            <td>{{$item->no_respone}}</td>
                                            <td>{{$item->created_at}}</td>
                                            <td>
                                                @if ($item->is_sinkronisasi == '0')
                                                    <button class="btn btn-sm btn-primary text-white" data-id="{{$item->id}}" id="saveButton"><i class="fa fa-refresh" aria-hidden="true"></i>
                                                        Singkronkan</button>
                                                @else
                                                    <a href="{{route('marketing.alumniprospectmaterial.detail', $item->id)}}" class="btn btn-sm btn-primary text-white">Jama'ah</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
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
    <script src="{{ asset('js/marketings/cs/prospect-material.js') }}"></script>
@endpush
