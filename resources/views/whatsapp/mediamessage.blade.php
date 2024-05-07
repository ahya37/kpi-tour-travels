@extends('layouts.app')
@section('title',$title ?? '')
@push('addon-styles')
@endpush
@section('content')
<div class="row">
    <div class="col-xl-12 col-lg-12">
        @include('layouts.notification')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{$title ?? ''}}</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{route('mediamessage.store')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Campaign name</label>
                                    <input class="form-control form-control-sm" name="campaign" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Numbers</label>
                                    <textarea class="form-control form-control-sm" name="contacts" rows="8" id="myContacts"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Message</label>
                                    <textarea class="form-control form-control-sm" name="message" rows="8" ></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Source</label>
                                   <select class="form-control form-control-sm">
                                    <option value="computer">From Computer</option>
                                    <option value="url">From URL</option>
                                   </select>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-sm  cs-bg-color cs-color-with"><i class="fa fa-paper-plane"></i> Kirim Pesan</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Media File</label>
                                    <input class="form-control form-control-sm" name="image" id="formFileSm" type="file">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('addon-scripts')
     <script>
        const textarea = document.getElementById('myContacts');
        textarea.setAttribute('placeholder', "+62890xxxx \n+62870xxxx \n+62830xxxx");
     </script>
@endpush