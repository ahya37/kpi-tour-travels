@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    {{-- SWEETALERT --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- CUSTOM CSS --}}
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/swal2.custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/programKerja/bulanan/index.css') }}" rel="stylesheet">
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
    <input type="hidden" id="roleName" value={{ Auth::user()->getRoleNames()[0] }}>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="card">
            <div class="card-header">
                <div class="row py-2">
                    <div class="col-sm-6">
                        <h4 style="margin: 0px;"><i class="fa fa-cog"></i> User Account Control</h4>
                    </div>
                    <div class="col-sm-6 text-right"></div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row mb-2">
                    <div class="col-sm-4">
                        <label>Nama</label>
                    </div>
                    <div class="col-sm-8">
                        @php
                            echo Auth::user()->name
                        @endphp
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-sm-4">
                        <label>Role</label>
                    </div>
                    <div class="col-sm-8">
                        @php
                            echo Auth::user()->getRoleNames()[0];
                        @endphp
                    </div>
                </div>

                <div class="card card-body mt-4">
                    <div class="row">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" onclick="showModal('modalChangePassword','','')">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalChangePassword">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title py-2">
                        <h4 style="margin: 0px;">Ubah Password user : @php  echo Auth::user()->name @endphp</h4>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <label class="pt-2">Password Lama</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="hidden" name="passwordAccount" id="passwordAccount" value="@php echo Auth::user()->id @endphp">
                            <input type="password" class="form-control" name="passwordLama" id="passwordLama" placeholder="Masukan Password Lama" onblur="checkCurrentPassword(this.value)">
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <label class="pt-2">Password Baru</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="password" name="passwordBaru" id="passwordBaru" class="form-control" placeholder="Masukan Password Baru">
                        </div>
                    </div>
                    <div class="form-row mb-2">
                        <div class="col-sm-4">
                            <label class="pt-2">Konfirmasi Password</label>
                        </div>
                        <div class="col-sm-8">
                            <input type="password" name="passwordKonfirmasi" id="passwordKonfirmasi" class="form-control" onkeyup="verifNewPassword(this.id)" placeholder="Masukan Kembali Password Baru" readonly>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-sm-12 px-2">
                            <button type="button" class="btn btn-secondary" onclick="closeModal('modalChangePassword')">Batal</button>
                            <button type="button" class="btn btn-primary" onclick="TransUbahPassword()">Ubah</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('addon-script')
    {{-- SWEETALERT2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.11.0/dist/sweetalert2.all.min.js"></script>
    {{-- CUSTOM JS --}}
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script src="{{ asset('js/users/userProfile/index.userProfile.js') }}"></script>
@endpush