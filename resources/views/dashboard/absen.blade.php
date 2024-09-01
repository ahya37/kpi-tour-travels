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
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/swal2.custom.css') }}">

    <style>
        video {
            width: 100%;
            height: 100%;
        }        
    </style>
</head>

<body class="gray-bg">

    <div class="d-flex flex-column align-items-center justify-content-center w-100">
        <input type="hidden" id="prs_user_id" value="{{ $user_id }}">
        <div style="width: 400px; height: 100%;">
            <div class="row mt-4 mb-4 text-center">
                <div class="col-sm-12">
                    <h2 class="no-margins"><label>Adhitya Dwi Cahyana</label></h2>
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
    
    <script src="{{ asset('js/dashboard/index.absen.js') }}"></script>

</body>

</html>
