<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="acc-token" content="{{ $accToken ?? '' }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title')</title>
	
	@stack('prepend-style')
	
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('assets/css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
	
	@stack('addon-style') 
</head>

<body>
 <div id="wrapper">
    
	@include('layouts.sidebar')

        <div id="page-wrapper" class="gray-bg">

		@include('layouts.navbar')
		
		@yield('breadcrumb')
       
        @yield('content')
				
        @include('layouts.footer')

        </div>
        </div>
                

	@stack('prepend-script')
    <!-- Mainly scripts -->
    <script src="{{asset('assets/js/jquery-3.1.1.min.js')}}"></script>
    <script src="{{asset('assets/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap.js')}}"></script>
    <script src="{{asset('assets/js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <script src="{{asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{asset('assets/js/inspinia.js')}}"></script>
    <!-- <script src="{{asset('assets/js/plugins/pace/pace.min.js')}}"></script> -->
    <script>
        const User = {{ Auth::user()->id }};
    </script>
    <script src="{{asset('js/app.js')}}"></script>
	
	<!-- ChartJS-->
	@stack('addon-script')
	
    </body>

</html>
