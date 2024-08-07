<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header text-center" style="padding:0px; background-repeat: round;">
                <div id="profile_id" style="margin-top: 32px; margin-bottom: 32px;">
                    <div class="row">
                        <div class="col-sm-12">
                            <img alt="image" class="rounded-circle" id="profile_image" width="64px" height="64px" src="{{ asset('assets/img/9187604.png') }}" />
                        </div>
                    </div>
                    <div class="row" style="padding-top: 16px;">
                        <div class="col-sm-12">
                            <span class="font-bold text-white">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="col-sm-12">
                            <span class="text-muted text-xs block">{{ strtoupper(auth()->user()->getRoleNames()[0])
                                }}</span>
                        </div>
                    </div>
                </div>
            </li>
            <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fa fa-home"></i> <span
                        class="nav-label">Dashboard</span></a>
            </li>
            @if (Auth::user()->hasRole('admin'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
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
                    <li class="{{ request()->is('master/programkerja/master_program') ? 'active' : '' }}">
                        <a href={{ route('programKerja.masterProgram.index') }}>Program</a>
                    </li>
                </ul>
				{{-- <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                </ul> --}}
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('marketings/prospectmaterial/') || request()->is('marketings/prospectmaterial/*') ? 'active' : '' }}"><a href="{{ route('marketing.prospectmaterial') }}">Generate Bahan Prospek Alumni</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek Alumni</a></li>
                </ul>
				  <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.haji.target') }}">Setting Target Haji</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.target') }}">Laporan Umrah</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.haji.report') }}">Laporan Haji</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.rencancakerja.report')}}">Laporan Rencana Kerja</a></li>
                </ul>
				
				<ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.pekerjaan.report') }}">Laporan Pekerjaan Harian</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                        <a href="{{ route('marketing.programkerja.dashboard') }}">Program Kerja</a></li>
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
                    <li
                        class="{{ request()->is('divisi/operasional') || request()->is('divisi/operasional/*') ? 'active' : '' }}">
                        <a href="{{ route('index.operasional') }}">Operasional</a>
                    </li>
                </ul>
            </li>
            @endif
            @if (Auth::user()->hasRole('marketing'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                    <li class="{{ request()->is('marketings/prospectmaterial/') || request()->is('marketings/prospectmaterial/*') ? 'active' : '' }}"><a href="{{ route('marketing.prospectmaterial') }}">Generate Bahan Prospek Alumni</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek Alumni</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.haji.target') }}">Setting Target Haji</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.target') }}">Laporan Umrah</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.haji.report') }}">Laporan Haji</a></li>
                </ul>
              
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketings.rencancakerja.report')}}">Laporan Rencana Kerja</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.pekerjaan.report') }}">Laporan Pekerjaan Harian</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                        <a href="{{ route('marketing.programkerja.dashboard') }}">Program Kerja</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/programKerja/jenisPekerjaan') ? 'active' : '' }}">
                <a href="{{ route('marketing.jenisPekerjaan.index') }}"><i class="fa fa-pencil"></i> <span
                        class="nav-label">Aktivitas Harian</span></a>
            </li>
            @endif
            @if (Auth::user()->hasRole('operasional'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-diamond"></i>
                    <span class="nav-label">Operasional</span> 
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/operasional') ? 'active' : '' }}"><a href="{{ route('index.operasional') }}">Dashboard</a></li>
                </ul>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/operasional/program') ? 'active' : '' }}"><a href="{{ route('index.operasional.program') }}">Jadwal Umrah</a></li>
                </ul>
                
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/operasional/rules') ? 'active' : '' }}"><a href="{{ route('index.operasional.rulesprokerbulanan') }}">Aturan Program Kerja</a></li>
                </ul>
            </li>
            {{-- <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                <a href="#">
                    <i class='fa fa-users'></i>
                    <span class="nav-label">Divisi</span><span class='fa arrow'></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ request()->is('divisi/operasional') || request()->is('divisi/operasional/*') ? 'active' : '' }}">
                        <a href="{{ route('index.operasional') }}">Operasional</a>
                    </li>
                </ul>
            </li> --}}
            @endif
            @if (Auth::user()->hasRole('it'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                </ul>
            </li>

            {{-- <li class="{{ request()->is('marketings/*') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">Marketing</span> <span
                        class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="active"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek
                            Alumni</a></li>
                    {{-- <li class="active"><a href="{{ route('marketing.workplans.index') }}">Rencana Kerja</a></li>
                    --}}
                </ul>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('marketings/laporan/*') ? 'active' : '' }}">
                        <a href="#">
                            <span class="nav-label">Laporan</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-third-level">
                            <li class="{{ request()->is('marketings/laporan/pelaksanaan_iklan') ? 'active' : '' }}">
                                <a href="{{ route('marketing.laporan.iklan') }}">
                                    <span class="nav-label">Laporan Iklan</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li> --}}

            <li class="{{ request()->is('aktivitas') ? 'active' : '' }}">
                <a href="{{ route('aktivitas.harian.index') }}"><i class="fa fa-pencil"></i> <span
                        class="nav-label">Aktivitas Harian</span></a>
            </li>
            @endif
            @if(Auth::user()->hasRole('umum'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->hasRole('finance'))
            {{-- <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li>
                </ul>
            </li> --}}
            <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-diamond"></i>
                    <span class="nav-label">Finance</span> 
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/finance') ? 'active' : '' }}"><a href="{{ route('index.finance') }}">Dashboard</a></li>
                </ul>
                {{-- <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/operasional/program') ? 'active' : '' }}"><a href="{{ route('index.operasional.program') }}">Jadwal Umrah</a></li>
                </ul>
                
                <ul class="nav nav-second-level">
                    <li class="{{ request()->is('divisi/operasional/rules') ? 'active' : '' }}"><a href="{{ route('index.operasional.rulesprokerbulanan') }}">Aturan Program Kerja</a></li>
                </ul> --}}
            </li>
            <li class="{{ request()->is('aktivitas') ? 'active' : '' }}">
                <a href="{{ route('aktivitas.harian.index') }}"><i class="fa fa-pencil"></i> <span
                        class="nav-label">Aktivitas Harian</span></a>
            </li>
            <li class="{{ request()->is('presensi/*') ? 'active' : '' }}">
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

            @if(Auth::user()->hasRole('digital'))
                <li class="{{ request()->is('divisi/digital/aktivitasHarian') ? 'active' : '' }}">
                    <a href="{{ route('index.programKerja.digital') }}">
                        <i class="fa fa-pencil"></i>
                        <span class="nav-label">Aktivitas Harian</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>

<script src="{{ asset('assets/js/jquery-3.1.1.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(()    => {
        var base_url    = window.location.origin;
        var default_picture     = base_url+"/assets/img/9187604.png";
        if(localStorage.length > 0) {
            const profile_pict  = localStorage.getItem('profile_pict');
            $("#profile_image").prop('src', '');
            $("#profile_image").prop('src', profile_pict);
        } else {
            $.ajax({
                cache   : false,
                type    : 'GET',
                url     : '/accounts/userProfiles/getDataUser',
                success : (success) => {
                    if(success.length > 0) {
                        localStorage.clear();
                        localStorage.setItem('profile_pict', success[0].pict_dir == null ? base_url + '/assets/img/9187604.png' : base_url + '/'+ success[0].pict_dir);
                        $("#profile_image").prop('src', success[0].pict_dir == null ? base_url + '/assets/img/9187604.png' : base_url + '/'+ success[0].pict_dir);
                    } else {
                        localStorage.clear();
                        localStorage.setItem('profile_pict', default_picture);
                        $("#profile_image").prop('src', default_picture);
                    }
                },
                error   : (err)     => {
                    console.log(err);
                }
            })
        }
    })
</script>