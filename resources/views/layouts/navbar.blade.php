<div class="row border-bottom">
	<nav class="navbar navbar-static-top" role="navigation">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#" title='Hide Sidebar' style="margin-top: 15px;"><i class="fa fa-bars"></i> </a>
        </div>

        <ul class="nav navbar-top-links navbar-right">
            <li class="dropdown" style="margin-right: 20px;">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user-circle" style="font-size: 18pt;"></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#">
                            <i class='fa fa-user'></i> Activity Log
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class='fa fa-cog'></i> Setting Profile
                        </a>
                    </li>
                    <li class="dropdown-divider"></li>
                    <li>
                        <a href="{{ route('logout.store') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-sign-out"></i> Log out
                        </a>
                        <form id="logout-form" action="{{ route('logout.store') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
	</nav>
</div>