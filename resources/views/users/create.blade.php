@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/sweetalert/sweetalert.css') }}" rel="stylesheet">
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
                    <div class="ibox-content">
                        <form id="form" action="{{ route('users.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Nama</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Konfirmasi Password</label>
                                <div class="col-sm-10">
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group  row"><label class="col-sm-2 col-form-label">Roles</label>
                                <div class="col-sm-10">
                                   @foreach ($roles as $item)
                                       <input type="checkbox" name="roles[]" value="{{$item->name}}" id="check-{{$item->id}}"> {{$item->name}}
                                   @endforeach
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <button type="reset" class="btn btn-sm btn-white">Batal</button>
                            <button type="submit" class="btn btn-sm btn-primary ladda-button">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('addon-script')
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/csrf-token.js') }}"></script>
@endpush
