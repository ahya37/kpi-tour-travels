<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
	<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    {{-- FONTAWESOME BARU --}}
    <link href="{{ asset('css/customCSS/fontawesome-5.8.2/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
    {{-- SELECT2 --}}
    <link href="{{ asset('assets/css/plugins/select2/select2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/plugins/select2/select2-bootstrap4.min.css') }}" rel="stylesheet">
    {{-- DATERANGEPICKER --}}
    <link rel="stylesheet" href="{{ asset('css/customCSS/daterangepicker/daterangepicker.css') }}">
    {{-- DATATABLES --}}
    <link href="{{ asset('assets/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customCSS/DataTables/fixedHeader-3.2.0/fixedHeader.dataTables.min.css') }}">

    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/swal2.custom.css') }}">

    <style>
        video {
            width: 100%;
            height: 100%;
        }
        .form-group > label {
            font-weight: bold;
        }
        .form-group > input.form-control {
            height: 38px;
        }
    </style>
</head>

<body class="gray-bg">

    <div class="d-flex flex-column align-items-center justify-content-center w-100">
        <input type="hidden" id="prs_user_id" value="{{ $user_id }}">
        <div style="width: 400px; height: 100%;" id="absen_view">
            <div class="row mt-4 mb-4 text-center">
                <div class="col-sm-12">
                    <h2 class="no-margins"><label>{{ $user_name }}</label></h2>
                </div>
            </div>
            <div class="card">
                <div class="card-header align-items-center text-left">
                    <h4 class="no-margins" id="abs_date">Absensi ...</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-12" style="height: 100px;">
                            <div class="d-flex flex-row align-items-center justify-content-center w-100 h-100">
                                <h2 class="no-margins" id="abs_clock"><label>00:00:00</label></h2>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <button class="btn btn-primary w-100" onclick="showModal('modal_open_cam', 'masuk', '')">
                                <i class="fa fa-sign-in"></i>&nbsp; Absen Masuk
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button class="btn btn-danger w-100" onclick="showModal('modal_open_cam', 'keluar', '')">
                                <i class="fa fa-sign-out"></i>&nbsp; Absen Pulang
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2 text-left">
                        <div class="col-sm-12">
                            <div class="d-flex flex-column w-100">
                                <span id="prs_text_masuk"></span>
                                <span id="prs_text_keluar"></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <a href="{{ route('logout.store') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-danger w-100">
                                <i class="fa fa-power-off"></i> Log out
                            </a>
                            <form id="logout-form" action="{{ route('logout.store') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <button class="btn btn-primary w-100" onclick="showView('pengajuan_cuti', 'add')"><i class="fa fa-edit"></i>&nbsp;Pengajuan Cuti</button>
                        </div>
                    </div>
                    <div class="row mb-2 d-none" id="back_button">
                        <div class="col-sm-12">
                            <a href="/dashboard" class="btn btn-success w-100">
                            <i class="fa fa-home"></i> Kembali ke Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2 text-left">
                <div class="col-sm-12">
                    <p class="text-danger">* Fitur Absen untuk masuk ke aplikasi</p>
                </div>
            </div>
        </div>
        <div style="width: 400px; height: 100%;" id="pengajuan_cuti" class="d-none">
            <div class="row mt-4 mb-4 text-center">
                <div class="col-sm-12">
                    <h2 class="no-margins"><label>{{ $user_name }}</label></h2>
                </div>
            </div>
            <div class="card">
                <div class="card-header align-items-center text-left">
                    <h4 class="no-margins">Pengajuan Cuti</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Uraian</label>
                                <input type="text" class="form-control" id="pgj_title" name="pgj_title" placeholder="Tulis Uraian" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Jenis Pengajuan</label>
                                <select name="pgj_type" id="pgj_type" class="form-control" style="width: 100%;"></select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Tanggal Pengajuan</label>
                                <input type="text" class="form-control" style="background: white; cursor: pointer;" id="pgj_date" name="pgj_date" placeholder="DD/MM/YYYY s/d DD/MM/YYYY" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Banyaknya Hari</label>
                                <input type="text" class="form-control" readonly placeholder="-- Hari" id="pgj_duration" name="pgj_duration">
                            </div>
                        </div>
                    </div>
                    <a href="#" onclick="showModal('modal_list_pengajuan', '', '')">Lihat Pengajuan Saya</a>
                    <hr>
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <button class="btn btn-primary w-100" id="btn_simpan_pgj" title="Simpan Pengajuan" onclick="simpanDataPengajuan(this.value)">
                                <i class="fa fa-save"></i>&nbsp; Simpan
                            </button>
                        </div>
                    </div>
                    <div class="row" id="back_to_index">
                        <div class="col-sm-12">
                            <button class="btn btn-secondary w-100" onclick="showView('absen_view')" title="Kembali ke Halaman Sebelumnya">
                                <i class="fa fa-chevron-left"></i>&nbsp; Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_open_cam">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" onclick="closeModal('modal_open_cam')">&times;</button>
                </div>
                <div class="modal-body h-100" id="body_camera">
                    <div class="row">
                        <div class="col-sm-12">
                            <video width="480" height="640" autoplay id="camera"></video>
                            <canvas id="takePhoto" style="width: 100%; height: 100%; border: 1px solid rgba(0, 0, 0, 0.2); padding: 8px;" class="d-none"></canvas>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center px-2">
                        <div class="col-sm-12">
                            <button class="btn btn-primary" onclick="shutterCamera()" id="btn_takePhoto">
                                <i class="fa fa-camera"></i> Ambil Foto
                            </button>
                            {{-- BUTTON CANCEL AND SAVE --}}
                            <button class="btn btn-danger d-none" id="btn_cancelData" onclick="batalSimpanData()">
                                <i class="fa fa-times"></i> Batal
                            </button>
                            <button class="btn btn-primary d-none" id="btn_simpanData" value="" onclick="getLocation(this.value)">
                                <i class="fa fa-save"></i> Simpan Data
                            </button>
                        </div>
                        
                    </div>
                </div>
                <div class="modal-body w-100 d-none" id="body_data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <canvas id="getPhoto" style="width: 438px; height: 338.5px; border: 1px solid rgba(0, 0, 0, 0.2); padding: 9px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_list_pengajuan">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="d-flex flex-row w-100 align-items-center justify-content-between">
                        <h4 class="no-margins"></h4>
                        <button class="close" onclick="closeModal('modal_list_pengajuan')">&times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-border" style="width: 100%;" id="table_list_pengajuan">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 5%;">No</th>
                                            <th class="text-center align-middle" style="width: 20%;">Tanggal</th>
                                            <th class="text-center align-middle">Keterangan</th>
                                            <th class="text-center align-middle" style="width: 14%;">Jenis</th>
                                            <th class="text-center align-middle" style="width: 15%;">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="{{asset('assets/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.js')}}"></script>
    {{-- SWEETALERT --}}
    <script src="{{ asset('js/customJS/SweetAlert/sweetalert2.all.min.js') }}"></script>
    {{-- MOMENT JS --}}
    <script src="{{ asset('js/customJS/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/id.js') }}"></script>
    <script src="{{ asset('js/customJS/moment/moment-timezone-with-data.min.js') }}"></script>
    {{-- FONTAWESOME --}}
    <script src="{{ asset('js/customJS/fontawesome-5.8.2/fontawesome.min.js') }}"></script>
    {{-- SELECT2 --}}
    <script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
    {{-- DATERANGEPICKER --}}
    <script src="{{ asset('js/customJS/daterangepicker/daterangepicker.min.js') }}"></script>
    {{-- DATATABLE --}}
    <script src="{{ asset('assets/js/plugins/dataTables/datatables.min.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/buttons.dataTables.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/dataTables.buttons.js') }}"></script>
    <script src="{{ asset('js/customJS/DataTables/fixedHeader-3.2.0/dataTables.fixedHeader.min.js') }}"></script>
    <script src="{{ asset('js/dashboard/index.absen.js') }}"></script>

</body>

</html>
