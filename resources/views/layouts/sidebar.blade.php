<nav class="navbar-default navbar-static-side" role="navigation">
        <div class="sidebar-collapse">
            <ul class="nav metismenu" id="side-menu">
                <li class="nav-header">
                    <div class="dropdown profile-element">
                        <img alt="image" class="rounded-circle" src="{{asset('assets/img/profile_small.jpg')}}"/>
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="block m-t-xs font-bold">{{auth()->guard('admin')->user()->name}}</span>
                            <span class="text-muted text-xs block">Developer<b class="caret"></b></span>
                        </a>
                        <ul class="dropdown-menu animated fadeInRight m-t-xs">
                            <li>
								<a class="dropdown-item"  href="{{ route('logoutstore') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>
								
								<form id="logout-form" action="{{ route('logoutstore') }}" method="POST" class="d-none">
									@csrf
								</form>
							</li>
                        </ul>
                    </div>
                </li>
				
				 <li class="{{request()->is('dashboard') ? 'active' : ''}}">
                     <a href="{{route('dashboard')}}"><i class="fa fa-bar-chart-o"></i> <span class="nav-label">Dashboard</span></a>
                 </li>
				 {{-- <li class="{{request()->is('marketing/*') ? 'active' : ''}}">
                        <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Dashboard</span> <span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="active"><a href="{{route('dashboard')}}">Marketing</a></li>

                        </ul>

                   </li> --}}
            </ul>

        </div>
    </nav>