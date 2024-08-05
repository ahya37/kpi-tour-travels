@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
    {{-- SWEETALERT --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- CUSTOM CSS --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/programKerja/bulanan/index.css') }}" rel="stylesheet">

    <style>
        .select2-selection, .select2-selection--multiple {
            padding: 4px 8px 4px 4px;
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
    @php
        setlocale(LC_ALL, 'IND');
    @endphp
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="card">
            <div class="card-header">
                <h4 style="margin:0px;">List Master Program</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary" onclick="showModal('modal_master_program', '', 'add')">Tambah Data</button>
                    </div>
                </div>
                <hr>
                <div class="row ">
                    <div class="col-sm-12">
                        <table class="table table-sm table-striped" style="width: 100%;" id="table_list_master_program">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">No</th>
                                    <th class="text-center align-middle">Nama Program</th>
                                    <th class="text-center align-middle">Berlaku Untuk</th>
                                    <th class="text-center align-middle">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_master_program">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title no-margin">Tambah Master Program</h4>
                    <button type="button" class="close" onclick="closeModal('modal_master_program')">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="hidden" name="master_program_id" id="master_program_id">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Untuk Divisi</label>
                                    <select class="form-control form-control-sm" name="master_program_divisi" id="master_program_divisi" data-placeholder="Pilih Divisi" style="width: 100%;" multiple="multiple"></select>
                                </div>
                                <div class="form-group mt-2">
                                    <label>Uraian</label>
                                    <input type="text" name="master_program_uraian" id="master_program_uraian" class="form-control" placeholder="Uraian" style="height: 37.5px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnCancel" onclick="closeModal('modal_master_program')">Tutup</button>
                    <button type="button" class="btn btn-primary" id="btnSave" value="" onclick="simpanData('modal_master_program', this.value)">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $('#exampleModal').on('show.bs.modal', event => {
            var button = $(event.relatedTarget);
            var modal = $(this);
            // Use above variables to manipulate the DOM
            
        });
    </script>
@endsection


@push('addon-script')
    @include('layouts.js')
    {{-- SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    {{-- CUSTOM JS --}}
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/master/programKerja/master_program/index.masterpogram.js') }}"></script>
@endpush