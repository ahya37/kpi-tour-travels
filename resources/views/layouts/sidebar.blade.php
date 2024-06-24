<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header text-center" style="padding:0px;">
                <div id="profile_id" style="margin-top: 32px; margin-bottom: 4px;">
                    <div class="row">
                        <div class="col-sm-12">
                            <img alt="image" class="rounded-circle" src="{{ asset('assets/img/9187604.png') }}" width="64px" height="64px"/>
                        </div>
                    </div>
                    <div class="row" style="padding-top: 16px;">
                        <div class="col-sm-12">
                            <span class="font-bold text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="text-muted text-xs block">{{ strtoupper(auth()->user()->getRoleNames()[0]) }}</span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa fa-bar-chart-o"></i> <span
                        class="nav-label">Dashboard</span></a>
            </li>
            @if (Auth::user()->hasRole('admin'))
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

                <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class='fa fa-users'></i>
                        <span class="nav-label">Divisi</span><span class='fa arrow'></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li><a href="#">Marketing</a></li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li><a href="#">IT</a></li>
                    </ul>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('divisi/operasional') || request()->is('divisi/operasional/*') ? 'active' : '' }}">
                            <a href="{{ route('index.operasional') }}">Operasional</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->hasRole('marketing'))
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
                        <ul class="nav nav-second-level">
                            <li class="active"><a href="{{ route('marketing.target') }}">Target Jamaah</a></li>
    
                        </ul>
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
            @if (Auth::user()->hasRole('operasional'))
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
                <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class='fa fa-users'></i>
                        <span class="nav-label">Divisi</span><span class='fa arrow'></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{ request()->is('divisi/operasional') || request()->is('divisi/operasional/*') ? 'active' : '' }}">
                            <a href="{{ route('index.operasional') }}">Operasional</a>
                        </li>
                    </ul>
                </li>
            @endif
            @if (Auth::user()->hasRole('it'))
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
            @if(Auth::user()->hasRole('umum'))
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
            @endif

            @if(Auth::user()->hasRole('finance'))
                <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-database"></i> <span class="nav-label">Presensi</span> <span
                            class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level">
                        <li class="{{ (request()->is('presensi') || request()->is('presensi/*')) ? 'active' : '' }}">
                            <a href={{ route('presensi.report') }}>Laporan</a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>

    </div>
</nav>
