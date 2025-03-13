<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header text-center" style="padding:0px; background-repeat: round;">
                <div class="profile-element">
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
                </div>
                <div class="logo-element">
                    <img alt="image" class="rounded-circle" id="profile_image" width="32px" height="32px" src="{{ asset('assets/img/9187604.png') }}" />
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
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('master/groupDivisions') ? 'active' : '' }}"><a href="{{ route('groupDivision.index') }}">Group Division</a></li>
                    <li class="{{ request()->is('master/subDivisions') ? 'active' : '' }}"><a href="{{ route('subDivisions.index') }}">Sub Division</a></li>
                    <li class="{{ request()->is('master/employees') ? 'active' : '' }}"><a href={{ route('Employees.index') }}>Employee</a></li>
                    <li class="{{ request()->is('master/programkerja/master_program') ? 'active' : '' }}"><a href={{ route('programKerja.masterProgram.index') }}>Program</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/*') || request()->is('umhaj/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-diamond"></i> 
                    <span class="nav-label">Marketing</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('marketings/alumniprospectmaterial') ? 'active' : '' }}"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek Alumni</a></li>
                    <li class="{{ request()->is('marketings/haji/target/create') ? 'active' : '' }}"><a href="{{ route('marketings.haji.target') }}">Setting Target Haji</a></li>
                    <li class="{{ request()->is('marketings/target') ? 'active' : '' }}"><a href="{{ route('marketing.target') }}">Laporan Umrah</a></li>
                    <li class="{{ request()->is('marketings/haji/report') ? 'active' : '' }}"><a href="{{ route('marketings.haji.report') }}">Laporan Haji</a></li>
                    <li class="{{ request()->is('marketings/rencanakerja/*') ? 'active' : '' }}"><a href="{{ route('marketings.rencancakerja.report')}}">Laporan Rencana Kerja</a></li>
                    <li class="{{ request()->is('master/programkerja/*') ? 'active' : '' }}"><a href="{{ route('marketing.pekerjaan.report') }}">Laporan Pekerjaan Harian</a></li>
                    <li class="{{ request()->is('marketings/programKerja/*') ? 'active' : '' }}"><a href="{{ route('marketing.programkerja.dashboard') }}">Program Kerja</a></li>
                    <li class="{{ request()->is('umhaj/*') ? 'active' : '' }}"><a href="{{ route('umhaj.dashboard') }}">Umhaj</a></li>
                    <li class="{{ request()->is('marketings/agent') || request()->is('marketings/agent/*') ? 'active' : '' }}"><a href="{{ route('marketing.agent') }}">Agent</a></li>
                </ul>
            </li>

            {{-- <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                <a href="{{ route('programKerja.bulanan.index') }}"><i class="fa fa-pencil"></i> <span
                        class="nav-label">Aktivitas Harian</span></a>
            </li> --}}

            {{-- <li class="{{ request()->is('accounts/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span class="nav-label">Accounts</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('accounts/permissions') || request()->is('accounts/permissions/*') ? 'active' : '' }}"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                    <li class="{{ request()->is('accounts/users') || request()->is('accounts/users/*') ? 'active' : '' }}"><a href="{{ route('users.index') }}">Users</a></li>
                    <li class="{{ request()->is('accounts/roles') || request()->is('accounts/roles/*') ? 'active' : '' }}"><a href="{{ route('roles.index') }}">Roles</a></li>
                </ul>
            </li> --}}

            <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                <a href="#">
                    <i class='fa fa-users'></i>
                    <span class="nav-label">Divisi</span><span class='fa arrow'></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class=""><a href="#">Marketing</a></li>
                    <li class="{{ request()->is('divisi/digital') || request()->is('divisi/digital/*') ? 'active' : '' }}"><a href="#">Digital</a></li>
                    <li class="{{ request()->is('divisi/operasional') || request()->is('divisi/operasional/*') ? 'active' : '' }}"><a href="{{ route('index.operasional') }}">Operasional</a></li>
                    <li class="{{ request()->is('divisi/finance') || request()->is('divisi/finance/*') ? 'active' : '' }}">
                        <a href="#"> Finance <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ request()->is('divisi/finance') || request()->is('disivi/finance/*') ? 'active' : ''}}"><a href="{{ route('index.finance') }}">Dashboard</a></li>
                            <li class="{{ request()->is('divisi/finance/master') || request()->is('divisi/finance/master/*') ? 'active' : '' }}"><a href="{{ route('finance.master.index') }}">Master</a></li>
                            <li class="{{ request()->is('divisi/finance/pembayaran') || request()->is('divisi/finance/pembayaran/*') ? 'active' : '' }}"><a href="{{ route('finance.pembayaran.index') }}">Pembayaran</a></li>
                        </ul>
                    </li>
                    {{-- <li class="{{ request()->is('divisi/human_resource') ? 'active' : '' }}"><a href="{{ route('index.human_resouce') }}">HR</a></li> --}}
                    <li class="{{ request()->is('divisi/human_resource') || request()->is('divisi/human_resource/*') ? 'active' : '' }}">
                        <a href="#">HR <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ request()->is('divisi/human_resource/dashboard') ? 'active' : '' }}"><a href="{{ route('index.human_resource.dashboard') }}">Dashboard</a></li>
                            <li class="{{ request()->is('divisi/human_resource/jam_kerja') ? 'active' : '' }}"><a href="{{ route('index.human_resource.jam_kerja') }}">Setting Jam Kerja</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="{{ request()->is('tarik_data/*') ? 'active' : '' }}">
                <a href="#">
                    <i class='fa fa-wrench'></i>
                    <span class="nav-label">Tarik Data</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('tarik_data/absensi') || request()->is('tarik_data/absensi/*') ? 'active' : '' }}"><a href="{{ route('index.tarik_data.absensi') }}">Absensi</a></li>
                    <li class="{{ request()->is('tarik_data/umhaj/*') ? 'active' : '' }}">
                        <a href="#">
                            <span class="nav-label">Umhaj</span>
                            <span class="fa arrow"></span>
                        </a>
                        <ul class="nav nav-third-level collapse">
                            <li class="{{ request()->is('tarik_data/umhaj/jadwal_umrah') ? 'active' : '' }}"><a href="{{ route('index.tarik_data.umhaj.jadwal_umrah') }}">Jadwal Umrah</a></li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="{{ request()->is('website') || request()->is('website/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-globe"></i>
                    <span class="nav-label">Perciktours.com</span>
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('website') ? 'active' : '' }}"><a href="{{ route('index.perciktours.com') }}">Dashboard</a></li>
                    <li class="{{ request()->is('website/assets/*') ? 'active' : '' }}"><a href="{{ route('index.management.assets') }}">Asset</a></li>
                </ul>
            </li>

            @endif
            {{-- U/ USER MARKETING --}}
            @if (Auth::user()->hasRole('marketing'))
            <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    {{-- <li
                        class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}">
                        <a href={{ route('programKerja.index') }}>Program Kerja</a>
                    </li> --}}
                    <li class="{{ request()->is('marketings/prospectmaterial/') || request()->is('marketings/prospectmaterial/*') ? 'active' : '' }}"><a href="{{ route('marketing.prospectmaterial') }}">Generate Bahan Prospek Alumni</a></li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/*') || request()->is('umhaj/*') || request()->is('divisi/marketing/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-diamond"></i> 
                    <span class="nav-label">Marketing</span> 
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('marketings/alumniprospectmaterial') ? 'active' : '' }}"><a href="{{ route('marketing.alumniprospectmaterial') }}">Bahan Prospek Alumni</a></li>
                    <li class="{{ request()->is('marketings/haji/target/create') ? 'active' : '' }}"><a href="{{ route('marketings.haji.target') }}">Setting Target Haji</a></li>
                    <li class="{{ request()->is('marketings/target') ? 'active' : '' }}"><a href="{{ route('marketing.target') }}">Laporan Umrah</a></li>
                    <li class="{{ request()->is('marketings/haji/report') ? 'active' : '' }}"><a href="{{ route('marketings.haji.report') }}">Laporan Haji</a></li>
                    <li class="{{ request()->is('marketings/rencanakerja/report') ? 'active' : '' }}"><a href="{{ route('marketings.rencancakerja.report')}}">Laporan Rencana Kerja</a></li>
                    <li class="{{ request()->is('master/programkerja/tahunan/marketing/report')  }}"><a href="{{ route('marketing.pekerjaan.report') }}">Laporan Pekerjaan Harian</a></li>
                    <li class="{{ request()->is('marketings/programKerja/Dashboard') ? 'active' : '' }}"><a href="{{ route('marketing.programkerja.dashboard') }}">Program Kerja</a></li>
                    <li class="{{ request()->is('umhaj/*') ? 'active' : '' }}"><a href="{{ route('umhaj.dashboard') }}">Umhaj</a></li>
                    <li class="{{ request()->is('marketings/agent') || request()->is('marketings/agent/*') ? 'active' : '' }}"><a href="{{ route('marketing.agent') }}">Agent</a></li>
                    <li class="{{ request()->is('divisi/marketing/pembayaran') || request()->is('divisi/marketing/pembayaran/*') ? 'active' : '' }}">
                        <a href="#"> Pembayaran <span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li class="{{ request()->is('divisi/marketing/pembayaran/haji') || request()->is('divisi/marketing/pembayaran/haji/*') ? 'active' : '' }}"><a href="{{ route('index.marketing.pembayaranHaji') }}">Pembayaran Haji</a></li>
                            <li class="{{ request()->is('divisi/marketing/pembayaran/umrah') || request()->is('divisi/marketing/pembayaran/umrah/*') ? 'active' : '' }}"><a href="{{ route('index.marketing.pembayaranUmrah') }}">Pembayaran Umrah</a></li>
                            {{-- <li class="{{ request()->is('divisi/finance/pembayaran') || request()->is('divisi/finance/pembayaran/*') ? 'active' : '' }}"><a href="{{ route('finance.pembayaran.index') }}">Pembayaran</a></li> --}}
                        </ul>
                    </li>
                </ul>
            </li>

            <li class="{{ request()->is('marketings/programKerja/jenisPekerjaan') ? 'active' : '' }}">
                <a href="{{ route('marketing.jenisPekerjaan.index') }}"><i class="fa fa-pencil"></i> <span
                        class="nav-label">Aktivitas Harian</span></a>
            </li>
            @endif

            {{-- U/ USER OPERASIONAL --}}
            @if (Auth::user()->hasRole('operasional'))
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
                    <span class="nav-label">Operasional</span> 
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('divisi/operasional') ? 'active' : '' }}"><a href="{{ route('index.operasional') }}">Dashboard</a></li>
                    <li class="{{ request()->is('divisi/operasional/program') ? 'active' : '' }}"><a href="{{ route('index.operasional.program') }}">Jadwal Umrah</a></li>
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
           
           {{-- U/ ROLE UMUM --}}
            @if(Auth::user()->hasRole('umum'))
            {{-- <li class="{{ request()->is('master/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-database"></i> <span class="nav-label">Master</span> <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ (request()->is('master/programkerja') || request()->is('master/programkerja/*')) ? 'active' : '' }}"><a href={{ route('programKerja.index') }}>Program Kerja</a></li>
                </ul>
            </li> --}}
            @endif

            {{-- U/ ROLE FINANCE --}}
            @if(Auth::user()->hasRole('finance'))
            <li class="{{ request()->is('divisi/*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-diamond"></i>
                    <span class="nav-label">Finance</span> 
                    <span class="fa arrow"></span>
                </a>
                <ul class="nav nav-second-level collapse">
                    <li class="{{ request()->is('divisi/finance') ? 'active' : '' }}"><a href="{{ route('index.finance') }}">Dashboard</a></li>
                    {{-- <li class="{{ request()->is('divisi/finance/aktivitas') ? 'active' : '' }}"><a href="{{ route('finance.index.aktivitas.harian') }}">Aktivitas Harian</a></li> --}}
                    <li class="{{ request()->is('divisi/finance/master') || request()->is('divisi/finance/master/*') ? 'active' : '' }}"><a href="{{ route('finance.master.index') }}">Master</a></li>
                    <li class="{{ request()->is('divisi/finance/pembayaran') || request()->is('divisi/finance/pembayaran') ? 'active' : '' }}"><a href="{{ route('finance.pembayaran.index') }}">Pembayaran</a></li>
                </ul>
            </li>
            @endif

            {{-- u/ USER DIGITAL --}}
            @if(Auth::user()->hasRole('digital'))
                <li class="{{ request()->is('divisi/digital/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-diamond"></i>
                        <span class="nav-label">Digital</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li class="{{ request()->is('divisi/digital/umrah/jadwal') ? 'active' : '' }}"><a href="{{ route('index.digital.jadwal_umrah') }}">Jadwal Umrah</a></li>
                        <li class="{{ request()->is('divisi/digital/aktivitasHarian') ? 'active' : '' }}"><a href="{{ route('index.programKerja.digital') }}">Aktivitas Harian</a></li>
                    </ul>
                </li>
                <li class="{{ request()->is('website') || request()->is('website/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class="fa fa-globe"></i>
                        <span class="nav-label">Perciktours.com</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li class="{{ request()->is('website') ? 'active' : '' }}"><a href="{{ route('index.perciktours.com') }}">Dashboard</a></li>
                        <li class="{{ request()->is('website/assets/*') ? 'active' : '' }}"><a href="{{ route('index.management.assets') }}">Asset</a></li>
                    </ul>
                </li>
            @endif

            {{-- HALAMAN YANG TIDAK PERLU ADMIN AKSES --}}
            @if(!Auth::user()->hasRole('admin'))
                <li class="{{ request()->is('pengajuan/*') ? 'active' : '' }}">
                    <a href="#">
                        <i class='fa fa-edit'></i>
                        <span class="nav-label">Pengajuan</span>
                        <span class="fa arrow"></span>
                    </a>
                    <ul class="nav nav-second-level collapse">
                        <li class="{{ request()->is('pengajuan/cuti') || request()->is('pengajuan/cuti/*') ? 'active' : '' }}"><a href="{{ route('index.pengajuan.cuti') }}">Cuti</a></li>
                        <li class="{{ request()->is('pengajuan/lembur') || request()->is('pengajuan/lembur/*') ? 'active' : '' }}"><a href="{{ route('index.pengajuan.lembur') }}">Lembur</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('absen.pulang') }}">
                        <i class="fa fa-sign-out"></i>
                        <span class="nav-label">Absensi Pulang</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</nav>

<script src="{{ asset('assets/js/jquery-3.1.1.min.js') }}"></script>

<script type="text/javascript">
    var base_url    = window.location.origin;
    var local_data  = [];
    $(document).ready(()    => {
        var storageExtract  = JSON.parse(localStorage.getItem('items'))[0];
        var pictDefault     = base_url + "/assets/img/9187604.png";

        if(storageExtract['profile_pict'] != '') {
            const profilePict   = storageExtract['profile_pict'];
            $("#profile_image").prop('src', '');
            $("#profile_image").prop('src', profilePict);
        } else {
            $.ajax({
                cache   : false,
                type    : 'GET',
                url     : '/accounts/userProfiles/getDataUser',
                success : (success) => {
                    if(success.length > 0) {
                        const sendData   = {
                            "email"         : storageExtract['email'],
                            "profile_pict"  : success[0].pict_dir == null ? pictDefault : base_url + '/'+ success[0].pict_dir,
                        };
                        local_data.push(sendData);
                        localStorage.setItem('items', JSON.stringify(local_data));
                        $("#profile_image").prop('src', success[0].pict_dir == null ? pictDefault : base_url + '/'+ success[0].pict_dir);
                    } else {
                        const sendData  = {
                            "email"         : storageExtract['email'],
                            "profile_pict"  : default_picture,
                        };
                        local_data.push(sendData);
                        localStorage.setItem('items', JSON.stringify(local_data));
                        $("#profile_image").prop('src', default_picture);
                    }
                },
                error   : (err)     => {
                    console.log(err);
                    $("#profile_image").prop('src', default_picture);
                }
            })
        }
    })
</script>