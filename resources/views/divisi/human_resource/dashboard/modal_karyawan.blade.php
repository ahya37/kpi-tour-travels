<div class="modal fade" id="modal_emp">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins">List Karyawan</h4>
                    <button class="close" onclick="closeModal('modal_emp')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary" id="filter_modal_emp" data-toggle="collapse" href="#list_filter_umrah"><i class="fa fa-filter"></i> Filter</button>
                        <button class="btn btn-primary" id="tambah_modal_emp" onclick="showModal('modal_employee_detail', 'add', '')"><i class="fa fa-plus"></i> Tambah Data</button>
                    </div>
                    <div class="col-12">
                        <div class="collapse" id="list_filter_umrah">
                            <div class="row border-1 mt-2">
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="filter_status_karyawan">Status Karyawan</label>
                                        <select name="filter_status_karyawan" id="filter_status_karyawan" class="form-control form-select" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-12">
                                    <div class="form-group">
                                        <label for="filter_jk_karyawan">Jenis Kelamin</label>
                                        <select name="filter_jk_karyawan" id="filter_jk_karyawan" class="form-control form-select" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table_emp" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 8%;">No</th>
                                        <th class="text-center align-middle">Nama</th>
                                        <th class="text-center align-middle" style="width: 25%;">Divisi</th>
                                        <th class="text-center align-middle" style="width: 10%;">Status</th>
                                        <th class="text-center align-middle" style="width: 8%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_employee_detail">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="no-margins font-weight-bold">Detail Karyawan <span id="title_employee_name"></span></h4>
                <button class="close" onclick="closeModal('modal_employee_detail')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="emp_form">
                    <div class="row">
                        <div class="col-xl-6 col-lg-12 col-12 border-sm-right border-0">
                            <div class="row border-right">
                                <div class="col-lg-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_first_name" class="font-weight-bold">Nama Depan</label>
                                        <input type="text" class="form-control" id="emp_first_name" name="emp_first_name" placeholder="Nama Depan">
                                        <input type="hidden" class="form-control" id="emp_id" name="emp_id" placeholder="ID">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_middle_name" class="font-weight-bold">Nama Tengah</label>
                                        <input type="text" class="form-control" id="emp_middle_name" name="emp_middle_name" placeholder="Nama Tengah">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_last_name" class="font-weight-bold">Nama Belakang</label>
                                        <input type="text" class="form-control" id="emp_last_name" name="emp_last_name" placeholder="Nama Belakang">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="emp_gender" class="font-weight-bold">Jenis Kelamin</label>
                                        <select name="emp_gender" id="emp_gender" class="form-control form-select" style="width: 100%;"></select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_bod" class="font-weight-bold">Tgl. Lahir</label>
                                        <input type="text" class="form-control" id="emp_bod" name="emp_bod" placeholder="Tgl. Lahir" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_birth_place" class="font-weight-bold">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="emp_birth_place" name="emp_birth_place" placeholder="Tempat Lahir">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="emp_degree" class="font-weight-bold">Pendidikan Terakhir</label>
                                        <select name="emp_degree" id="emp_degree" class="form-control form-select" style="width: 100%;"></select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="font-weight-normal ml-1" style="cursor:pointer; color:blue;" title="Lihat File">Lihat File</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-12">
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_join_date" class="font-weight-bold">Tgl. Bergabung</label>
                                        <input type="text" class="form-control" id="emp_join_date" name="emp_join_date" placeholder="Tgl. Bergabung" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="emp_work_periode" class="font-weight-bold">Masa Kerja</label><br>
                                        <span id="emp_work_periode">0 Tahun 0 Bulan</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="emp_group_division">Divisi</label>
                                        <select name="emp_group_division" id="emp_group_division" class="form-control form-select" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                                    </div>
                                    <div class="form-group">
                                        <label for="emp_sub_division">Sub-Divisi</label>
                                        <select name="emp_sub_division" id="emp_sub_division" class="form-control form-select" style="width: 100%;"></select>
                                    </div>
                                    <div class="form-group d-none">
                                        <label for="emp_role">Role System</label>
                                        <input type="text" id="emp_role" name="emp_role" class="form-control" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="emp_status">Status Karyawan</label>
                                        <select name="emp_status" id="emp_status" class="form-control form-select" style="width: 100%;"></select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="button" class="btn d-none" id="btnAktif_employeeDetail" value="" onclick="doSimpan('aktivasi', '', this.value)"></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="form_table_history" class="d-none">
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h2 class="font-weight-thin mt-0 mb-3">Tabel Riwayat Jabatan Karyawan</h2>
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-bordered" id="table_history_employee" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" style="width: 15%;">Perubahan Ke</th>
                                            <th class="text-center align-middle" >Divisi / Jabatan</th>
                                            <th class="text-center align-middle" style="width: 25%;">Tgl. Perubahan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modal_employee_detail')">Batal</button>
                <button class="btn btn-primary" id="btnSimpan_employeeDetail" value="" onclick="doSimpan('form_employee', this.value, '')">Simpan</button>
            </div>
        </div>
    </div>
</div>