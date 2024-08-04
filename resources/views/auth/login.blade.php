<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('/assets/login/fonts/icomoon/style.css')}}">

    <link rel="stylesheet" href="{{asset('/assets/login/css/owl.carousel.min.css')}}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('/assets/login/css/bootstrap.min.css')}}">
    
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{asset('/assets/login/css/style.css')}}">
    <title>ERP Percik Tours | Login</title>
  </head>
  <style class="text/css">
    input {
      background-color: #faf9f6;
    }
  </style>
  <body>
  

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-image: url('/assets/login/images/b_3.jpg');"></div>
    <div class="contents order-2 order-md-1">

      <div class="container">
        <div class="row align-items-center justify-content-center">
          <div class="col-md-7">
            <div class="mb-4 text-center">
              <h2>ERP Percik Tours Login</h2>
            </div>
            <form  method="POST" action="{{ route('login.store') }}">
                @csrf
              <div class="form-group first">
                <label for="username">Email / Username</label>
                <input type="email" name="email"  class="form-control" id="email" autofocus autocomplete="off" style="background-color: #faf9f6;">

              </div>
              <div class="form-group last mb-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" name="password" id="password" style="background-color: #faf9f6;">
              </div>
              <button type="submit" value="Log In" class="btn btn-block btn-sm btn-primary">Sign In</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    
  </div>
    
    

    <script src="{{asset('/assets/login/js/jquery-3.3.1.min.js')}}"></script>
    <script src="{{asset('/assets/login/js/popper.min.js')}}"></script>
    <script src="{{asset('/assets/login/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('/assets/login/js/main.js')}}"></script>

    <script type="text/javascript">
      localStorage.clear();
    </script>
  </body>
</html>