@extends('layouts.app')
@push('prepend-styles')
<link rel="stylesheet" href="{{asset('assets//vendor/chartist/css/chartist.min.css')}}">
<link href="{{asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css')}}" rel="stylesheet">
<link href="{{asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
@endpush
@section('content')
<div class="form-head d-flex flex-wrap mb-sm-4 mb-3 align-items-center">
    <div class="me-auto  d-lg-block mb-3">
        <h2 class="text-black mb-0 font-w700">Dashboard</h2>
        <p class="mb-0">Lorem ipsum  dolor sit amet </p>
    </div>
    <div class="dropdown custom-dropdown mb-3">
        <div class="btn btn-sm date-ds-btn btn-rounded d-flex align-items-center svg-btn me-3" data-bs-toggle="dropdown">
            <svg class="primary-icon" width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22.1669 5.83362H21.0003V3.50028C21.0003 3.19087 20.8773 2.89412 20.6585 2.67533C20.4398 2.45653 20.143 2.33362 19.8336 2.33362C19.5242 2.33362 19.2274 2.45653 19.0086 2.67533C18.7898 2.89412 18.6669 3.19087 18.6669 3.50028V5.83362H9.33359V3.50028C9.33359 3.19087 9.21067 2.89412 8.99188 2.67533C8.77309 2.45653 8.47634 2.33362 8.16692 2.33362C7.8575 2.33362 7.56076 2.45653 7.34196 2.67533C7.12317 2.89412 7.00025 3.19087 7.00025 3.50028V5.83362H5.83359C4.90533 5.83362 4.01509 6.20237 3.35871 6.85874C2.70234 7.51512 2.33359 8.40536 2.33359 9.33362V10.5003H25.6669V9.33362C25.6669 8.40536 25.2982 7.51512 24.6418 6.85874C23.9854 6.20237 23.0952 5.83362 22.1669 5.83362Z" fill="#1E33F2"/>
                <path d="M2.33359 22.1669C2.33359 23.0952 2.70234 23.9854 3.35871 24.6418C4.01509 25.2982 4.90533 25.6669 5.83359 25.6669H22.1669C23.0952 25.6669 23.9854 25.2982 24.6418 24.6418C25.2982 23.9854 25.6669 23.0952 25.6669 22.1669V12.8336H2.33359V22.1669Z" fill="#1E33F2"/>
            </svg>
            <div class="text-start ms-3">
                <span class="d-block font-w700">Change period</span>
                <small class="d-block">August 28th - October 28th, 2021</small>
            </div>
            <i class="fa fa-caret-down scale5 ms-3"></i>
        </div>
        <div class="dropdown-menu dropdown-menu-end">
            <a class="dropdown-item" href="#">October 29th - November 29th, 2021</a>
            <a class="dropdown-item" href="#">July 27th - Auguts 27th, 2021</a>
        </div>
    </div>	
    <a href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#addOrderModal" class="btn btn-primary btn-rounded mb-3"><i class="fa fa-user-plus me-3"></i>New Admission</a>
    <!-- Add Order -->
    <div class="modal fade" id="addOrderModal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label class="text-black font-w500">Project Name</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="text-black font-w500">Dadeline</label>
                            <input type="date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="text-black font-w500">Client Name</label>
                            <input type="text" class="form-control">
                        </div>
                        
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
              </div>
            </div>
        </div>
    </div>
</div>

@endsection
@push('prepend-scripts')
     <!-- Required vendors -->
     <script src="{{asset('assets/vendor/global/global.min.js')}}"></script>
     <script src="{{asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js')}}"></script>	
     <script src="{{asset('assets/vendor/bootstrap-datetimepicker/js/moment.js')}}"></script>
     <script src="{{asset('assets/vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
     <script src="{{asset('assets/vendor/peity/jquery.peity.min.js')}}"></script>
     <!-- Apex Chart -->
     <script src="{{asset('assets/vendor/apexchart/apexchart.js')}}"></script>
     <!-- Dashboard 1 -->
     <script src="{{asset('assets/js/dashboard/dashboard-1.js')}}"></script>
      <script src="{{asset('assets/js/custom.min.js')}}"></script>
     <script src="{{asset('assets/js/deznav-init.js')}}"></script>
@endpush