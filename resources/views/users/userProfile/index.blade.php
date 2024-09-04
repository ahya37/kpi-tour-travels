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
    <input type="hidden" value="@php echo Auth::user()->name @endphp">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm m-t-sm">
            <div class="col-sm-6 m-t-sm">
                <div class="d-flex flex-row align-items-center">
                    <div class="profile-image">
                        <div id="img_loading">
                            <div class="rounded-circle circle-border text-center bg-white" alt="Profile" style="width: 96px; height: 96px; padding: 8px;">
                                <div class="rounded-circle circle-border text-center bg-primary" style="width: 80px; height: 80px; padding-top: 24px;">
                                    <span class="spinner-border"></span>
                                </div>
                            </div>
                        </div>
                        {{-- KONDISI KETIKA TIDAK ADA PROFILE PICTURE --}}
                        <div id="img_not_found" class="d-none">
                            <div class="rounded-circle circle-border text-center bg-white" alt="Profile" style="width: 96px; height: 96px; padding: 8px;">
                                <div class="rounded-circle circle-border text-center bg-primary" style="width: 80px; height: 80px; padding-top: 4px;">
                                    <span style="font-weight: bold; font-size: 48px;" id="default_user_profiles">
                                        @php echo strtoupper(substr(Auth::user()->name, 0, 1)) @endphp
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- KONDISI KETIKA ADA PROFILE PICTURE --}}
                        <div id="img_found" class="d-none">
                            <div class="rounded-circle circle-border text-center bg-white" style="width: 96px; height: 96px; padding: 8px;" id="img_found_image">
                                {{-- <img src="{{ asset('assets/img/9187604.png') }}" class="rounded-circle m-b-md" alt="profile" style="width: 80px; height: 80px;"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="profile-info no-margins">
                        <h2 class="no-margins">
                            <span id="user_profiles_names">
                                @php echo Auth::user()->name @endphp
                            </span>
                        </h2>
                        <h4 id="user_rofiles_roles">
                            @php echo strtoupper(Auth::user()->getRoleNames()[0]) @endphp
                        </h4>
                        <h5 id="user_profiles_sub_division">
                            <i class="fa fa-spinner fa-spin"></i>
                        </h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <table class="table m-b-xs">
                    <tbody>
                        <tr>
                            <td colspan="2" class="text-center font-bold">Aktivitas Tahun 2024</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong id="total_act"><i class='fa fa-spinner fa-spin'></i></strong>&nbsp;Total Aktivitas <i class='fa'>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong id="total_act_curr_month"><i class="fa fa-spinner fa-spin"></i></strong>&nbsp;Aktivitas Bulan Ini
                            </td>
                            <td>
                                <strong id="total_act_last_month"><i class="fa fa-spinner fa-spin"></i></strong>&nbsp;Ativitas Bulan Kemarin
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-sm-12">
                <div class="card card-body">
                    <div class="d-flex flex-row align-items-center">
                        <button class="btn btn-primary mr-3" onclick="showModal('modal_change_pict', '{{ Auth::user()->getRoleNames()[0] }}')">Ubah Foto</button>
                        <button class="btn btn-primary" onclick="showModal('modalChangePassword', '{{ Auth::user()->getRoleNames()[0] }}')">Ubah Password</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins">
                            <i class="fa fa-info-circle"></i> Aktivitas Terakhir
                        </h4>
                    </div>
                    <div class="card-body">
                        <div id="table-loading" style="height: 364px;">
                            <div class="d-flex flex-column align-items-center w-100 h-100 justify-content-center">
                                <span class="spinner-border"></span><br>
                                <label>Table Sedang Dimuat</label>
                            </div>
                        </div>
                        <div id="table-show" class="d-none">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered table-striped table-hover" style="width: 100%; height: 100%;" id="tableLastActUser">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%;">No</th>
                                            <th class="text-center align-middle" style="width: 30%;">Tanggal</th>
                                            <th class="text-left align-middle">Uraian Aktivitas</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="no-margins"><i class='fa fa-bar-chart'></i> Chart Aktivitas Tahunan</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-loading" style="height: 364px;">
                            <div class="d-flex flex-column align-items-center w-100 h-100 justify-content-center">
                                <span class="spinner-border"></span><br>
                                <label>Chart Sedang Dimuat</label>
                            </div>
                        </div>
                        <div id="chart-show" class="d-none" style="height: 364px;">
                            <canvas id="chartActUser" style="width: 100%; height: auto;"></canvas>
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

    <div class="modal fade" id="modal_change_pict">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Ubah Foto Profil</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Upload File </label>
                                <input type="file" value="Pilih File" class="form-control-file" onchange="uploadPreview(this)" id="input_file">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Preview</label>
                                <div class="card card-body" style="padding-left: 128px;">
                                    <img id="preview_img" width="150px" height="150px" class="no-border" src="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modal_change_pict')">Batal</button>
                    <button type="submit" class="btn btn-primary" onclick="TransUploadImage();">Upload</button>
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
    {{-- DATATABLE --}}
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    {{-- CHART JS --}}
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>
    {{-- MOMENT --}}
    <script src="{{ asset('js/customJS/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/id.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/moment-timezone-with-data.min.js') }}"></script>
@endpush