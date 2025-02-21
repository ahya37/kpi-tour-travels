@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
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
                <div class="card">
                    <div class="card-header d-flex flex-row align-items-center justify-content-start">
                        <h4 class="no-margins card-title">Tabel Akun Bank</h4>
                        {{-- <button class="btn btn-sm btn-primary" title="Tambah Data" onclick="showModal('modal_form_coa', 'add', '')"><i class="fa fa-plus"></i> Tambah COA</button> --}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" style="width: 100%;" id="table_list_bank_account">
                                        <thead>
                                            <tr>
                                                <th class="text-center align-middle" style="width: 8%;">No</th>
                                                <th class="text-center align-middle">Nama Bank</th>
                                                <th class="text-center align-middle" style="width: 20%;">No. Rekening</th>
                                                <th class="text-center align-middle" style="width: 20%;">Kode CoA</th>
                                                <th class="text-center align-middle" style="width: 8%;">Aksi</th>
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
    <script src="{{ asset('js/divisi/finance/master/bank_account/bank_account.index.js') }}"></script>
@endpush