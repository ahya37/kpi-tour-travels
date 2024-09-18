@extends('layouts.app')
@section('title', $title ?? '')

@push('addon-style')
    @include('layouts.css')
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
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h4 class="no-margins">Simulasi Perhitungan Lembur</h4>
                    </div>
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label class="font-weight-bold">Gaji Pokok</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="number" class="form-control" step="1" min="0" inputmode="numeric" id="gaji_pokok" placeholder="Masukkan Gaji Pokok">
                                    </div>
                                    <div class="col-sm-2">
                                        <button class="btn btn-primary btn-sm" style="height: 36px;">Hitung</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Upah Lembur : Gaji Pokok * (1/173)</label>
                                    <h2 id="upah_lembur">Rp. 0</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Overtime 1</label>
                                    <div>16.00 s/d 17.00</div>
                                    <div id="ovt_1">Rp. 0</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Overtime 2</label>
                                    <div>17.00 s/d 18.00</div>
                                    <div id="ovt_2">Rp. 0</div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="font-weight-bold">Overtime 3</label>
                                    <div>18.00 s/d N/A</div>
                                    <div id="ovt_3">Rp. 0</div>
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
    <script type="text/javascript">
        $(document).ready(()=> {
             $("#gaji_pokok").on('keyup', () => {
                let gaji_pokok      = $("#gaji_pokok").val();
                let upah_lembur     = gaji_pokok * (1/173);
                let ovt_1           = 1.5 * upah_lembur;
                let ovt_2           = 2 * upah_lembur;
                let ovt_3           = 2 * upah_lembur;
                
                function RpUpah(number)
                {
                    return new Intl.NumberFormat('id-ID', {
                        style   : 'currency',
                        currency: 'IDR'
                    }).format(number);
                }
                
                $("#upah_lembur").html(RpUpah(upah_lembur));
                $("#ovt_1").html(RpUpah(ovt_1));
                $("#ovt_2").html(RpUpah(ovt_2));
                $("#ovt_3").html(RpUpah(ovt_3));
             })
        })
    </script>
    {{-- <script src="{{ asset('js/activities/tarik_data/absensi/index.tarik_data.absensi.js') }}"></script> --}}
@endpush