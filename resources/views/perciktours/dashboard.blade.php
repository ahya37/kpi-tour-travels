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
        <h1>Kategori</h1>
        <div class="row">
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title no-margins">Produk</h3>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins font-weight-bold" id="sum_total_produk">0</h2>
                        <small>Total Produk</small>
                    </div>
                    <div class="card-footer" style="cursor:pointer;" title="Lihat Detail">
                        <span>Lihat Detail</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header bg-success">
                        <h3 class="card-title no-margins">Program</h3>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins font-weight-bold" id="sum_total_program">0</h2>
                        <small>Total Program</small>
                    </div>
                    <div class="card-footer" style="cursor: pointer;" title="Lihat Detail">
                        <span>Lihat Detail</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-xl-0 mb-2">
                <div class="card">
                    <div class="card-header" style="background-color: var(--indigo); color: white;">
                        <h3 class="card-title no-margins">Aktif Program ({{ date('Y') }})</h3>
                    </div>
                    <div class="card-body">
                        <h2 class="no-margins font-weight-bold" id="sum_total_aktif_program">0</h2>
                        <small>Total Program Aktif</small>
                    </div>
                    <div class="card-footer" style="cursor: pointer;" title="Lihat Detail" onclick="showModal('modal_active_program_umrah')">
                        <span>Lihat Detail</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-4 col-md-6 col-12">
                {{-- SOON --}}
            </div>
        </div>
        <hr>
        <h1>Tarik Data</h1>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title no-margins">Rekapitulasi Data</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3 text-center">
                                <h4 class="no-margins">Jemaah</h4>
                                <h4 class="no-margins" id="sum_total_jemaah">0</h4>
                            </div>
                            <div class="col-3 text-center">
                                <h4 class="no-margins">Perjalanan</h4>
                                <h4 class="no-margins" id="sum_total_perjalanan">0</h4>
                            </div>
                            <div class="col-3 text-center">
                                <h4 class="no-margins">Pembimbing</h4>
                                <h4 class="no-margins" id="sum_total_pembimbing">0</h4>
                            </div>
                            <div class="col-3 text-center">
                                <h4 class="no-margins">Agen</h4>
                                <h4 class="no-margins" id="sum_total_agen">0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-row justify-content-between align-items-center">
                        <small class="no-margins">Update Terakhir <span id="sum_last_update" class="font-weight-bold"></span></small>
                        <button class="btn btn-sm btn-primary" title="Tarik Data" onclick="doTarikData('summary_data')" id="btn_summary_tarik"><i class="fa fa-sync"></i> Perbarui Data</button>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="card">
                    <div class="card-header d-flex flex-row align-items-center justify-content-between w-100">
                        <h3 class="card-title no-margins">Table Jadwal Umrah</h3>
                        <button class="btn btn-sm btn-primary d-none" id="refresh_table_jadwal_umrah" onclick="doTarikData('refresh_table_umrah')"><i class="fa fa-sync"></i></button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" id="table_jadwal_umrah">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tour Code</th>
                                        <th class="text-center align-middle">Keberangkatan</th>
                                        <th class="text-center align-middle">Program</th>
                                        <th class="text-center align-middle">Seat</th>
                                        <th class="text-center align-middle">Terisi</th>
                                        <th class="text-center align-middle">Tersedia</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-row align-items-center justify-content-between">
                        <small id="last_update_jadwal_umrah">Update Terakhir</small>
                        <button class="btn btn-sm btn-primary" id="btn_jadwal_tarik" onclick="doTarikData('jadwal_umrah')" title="Perbarui Data"><i class="fa fa-sync"></i> Perbarui Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="modal_detail_jadwal">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                    <h3 class="no-margins font-weight-bold">Detail Jadwal Umrah <span id="tour_code_title"></span></h3>
                    <button class="close" onclick="closeModal('modal_detail_jadwal')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xl-6 col-12">
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Tour Code</h4>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control" id="detail_tour_code" placeholder="Tour Code" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Progarm</h4>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control" id="detail_program" placeholder="Nama Program" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Tanggal Berangkat</h4>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control" id="detail_keberangkatan" placeholder="DD/MM/YYYY" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Tanggal Kepulangan</h4>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control" id="detail_kepulangan" placeholder="DD/MM/YYYY" readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Pembimbing</h4>
                                </div>
                                <div class="col-8">
                                    <input type="text" class="form-control" id="detail_pembimbing" placeholder="Nama Pembimbing" readonly>
                                </div>
                            </div>
                            {{-- IF FLYER DIDNT EXIST --}}
                            {{-- <form id="uploadFlyer" enctype="multipart/form-data">
                                <div class="row mb-2">
                                    <div class="col-4 mt-2">
                                        <h4 class="font-weight-bold no-margins">Flyer</h4>
                                    </div>
                                    <div class="col-8">
                                        <input type="hidden" id="tour_code" name="tour_code">
                                        <input type="file" class="form-control-file" id="detail_flyer" name="detail_flyer">
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-4 mt-2">&nbsp;</div>
                                    <div class="col-8">
                                        <button type="button" class="btn btn-primary" onclick="doUploadFlyer();"><i class="fa fa-upload"></i> Upload</button>
                                    </div>
                                </div>
                            </form> --}}
                            {{-- IF FLYER EXIST --}}
                            {{-- <div class="row mb-2 d-none" id="flyer_exist">
                                <div class="col-4 mt-2">
                                    <h4 class="no-margins font-weight-bold">Flyer</h4>
                                </div>
                                <div class="col-8">
                                    <a href="#" id="flyer_exist_link" title="Download Flyer" target="_blank" class="btn btn-primary"></a>
                                </div>
                            </div> --}}
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Update Terakhir</h4>
                                </div>
                                <div class="col-8 mt-2">
                                    <h4 class="no-margins font-weight-normal" id="detail_update_terakhir">{{ date('Y-m-d') }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Jumlah Seat</h4>
                                </div>
                                <div class="col-7">
                                    <input type="text" class="form-control text-right" id="detail_jml_seat" placeholder="Jumlah Seat" readonly>
                                </div>
                                <div class="col-1 mt-2">
                                    <h4 class="font-weight-normal no-margins">Seat</h4>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Seat Tersedia</h4>
                                </div>
                                <div class="col-7">
                                    <input type="text" class="form-control text-right" id="detail_seat_avail" placeholder="Seat Tersedia" readonly>
                                </div>
                                <div class="col-1 mt-2">
                                    <h4 class="font-weight-normal no-margins">Seat</h4>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4 mt-2">
                                    <h4 class="font-weight-bold no-margins">Seat Terambil</h4>
                                </div>
                                <div class="col-7">
                                    <input type="text" class="form-control text-right" id="detail_seat_use" placeholder="Seat Terambil" readonly>
                                </div>
                                <div class="col-1 mt-2">
                                    <h4 class="font-weight-normal no-margins">Seat</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('perciktours.modal_active_program_umrah');
    @include('perciktours.modal_active_program_umrah_form');
@endsection


@push('addon-script')
    @include('layouts.js')
    <script src="{{ asset('js/csrf-token.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/perciktours/dashboard.js') }}"></script>
@endpush