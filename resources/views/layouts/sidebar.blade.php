<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" class="rounded-circle" src="{{ asset('assets/img/profile_small.jpg') }}" />
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold">{{ auth()->user()->name }}</span>
                        <span class="text-muted text-xs block">Developer<b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a class="dropdown-item" href="{{ route('logout.store') }}"
                                onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a>

                            <form id="logout-form" action="{{ route('logout.store') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </li>
            @if (Auth::user()->hasRole('admin'))
                <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fa fa-bar-chart-o"></i> <span
                            class="nav-label">Dashboard</span></a>
                </li>
                <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span
                            class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('master/groupDivisions') ? 'active' : '' }}">
                            <a href="{{ route('groupDivision.index') }}">Group Division</a>
                        </li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('master/subDivisions') ? 'active' : '' }}">
                            <a href="{{ route('subDivisions.index') }}">Sub Division</a>
                        </li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('master/employees') ? 'active' : '' }}">
                            <a href={{ route('Employees.index') }}>Employee</a>
                        </li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                            <a href={{ route('programKerja.index') }}>Program Kerja</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                            class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('marketing.target') }}">Target Jamaah</a></li>

                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('marketing.prospectmaterial') }}">Bahan Prospek</a></li>
                    </ul>
                </li>

                {{-- <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('programKerja.bulanan.index') }}"><i class="fa fa-pencil"></i> <span
                            class="nav-label">Aktivitas Harian</span></a>
                </li> --}}

                <li class="{{ request()->is('accounts/*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-users"></i><span class="nav-label">Accounts</span> <span
                            class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('users.index') }}">Users</a></li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('roles.index') }}">Roles</a></li>
                    </ul>
                </li>


            @endif
            @if (Auth::user()->hasRole('marketing'))
                <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fa fa-bar-chart-o"></i> <span
                            class="nav-label">Dashboard</span></a>
                </li>

                <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span
                            class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                            <a href={{ route('programKerja.index') }}>Program Kerja</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                            class="fa arrow"></span></a>
                    {{-- <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek
                                Alumni</a></li>
                        <li class="active"><a href="{{ route('marketing.workplans.index') }}">Rencana Kerja</a></li>
                    </ul> --}}
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('marketings/laporan/*') ? 'active' : '' }}">
                            <a href="#">
                                <span class="nav-label">Laporan</span>
                                <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-third-level">
                                <li
                                    class="{{ request()->is('marketings/laporan/pelaksanaan_iklan') ? 'active' : '' }}">
                                    <a href="{{ route('marketing.laporan.iklan') }}">
                                        <span class="nav-label">Laporan Iklan</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->is('aktivitas') ? 'active' : '' }}">
                    <a href="{{ route('aktivitas.harian.index') }}"><i class="fa fa-pencil"></i> <span
                            class="nav-label">Aktivitas Harian</span></a>
                </li>
            @endif
            @if (Auth::user()->hasRole('it'))
                <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fa fa-bar-chart-o"></i> <span
                            class="nav-label">Dashboard</span></a>
                </li>

                <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span
                            class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                            <a href={{ route('programKerja.index') }}>Program Kerja</a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                    <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                            class="fa arrow"></span></a>
                    <ul class="nav nav-second-level">
                        <li class="active"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek
                                Alumni</a></li>
                        {{-- <li class="active"><a href="{{ route('marketing.workplans.index') }}">Rencana Kerja</a></li> --}}
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('marketings/laporan/*') ? 'active' : '' }}">
                            <a href="#">
                                <span class="nav-label">Laporan</span>
                                <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-third-level">
                                <li
                                    class="{{ request()->is('marketings/laporan/pelaksanaan_iklan') ? 'active' : '' }}">
                                    <a href="{{ route('marketing.laporan.iklan') }}">
                                        <span class="nav-label">Laporan Iklan</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->is('aktivitas') ? 'active' : '' }}">
                    <a href="{{ route('aktivitas.harian.index') }}"><i class="fa fa-pencil"></i> <span
                            class="nav-label">Aktivitas Harian</span></a>
                </li>
            @endif
        </ul>

    </div>
</nav>
