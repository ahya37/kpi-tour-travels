@extends('layouts.app')
@section('title',$title)
@push('addon-styles')
    <link href="{{asset('assets/vendor/dropzone/dist/dropzone.css')}}" rel="stylesheet">
@endpush
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        @include('layouts.notification')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Qr Code</h4>
            </div>
            <div class="card-body">
                <form action="{{route('qrcode.generate')}}" method="POST" id="qrcode-generate">
                    @csrf 
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-sm  cs-bg-color cs-color-with"><i class="fa fa-qrcode"></i> Generate</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="Loading" class="d-none"></div>
                            <img id="imgqrcode">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('addon-scripts')
<script type="text/javascript" src="{{asset('js/qrcode-create.js')}}"></script>
@endpush