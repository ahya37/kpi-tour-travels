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
                                <div class="col-xl-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_first_name" class="font-weight-bold">Nama Depan</label>
                                        <input type="text" class="form-control" id="emp_first_name" name="emp_first_name" placeholder="Nama Depan">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_middle_name" class="font-weight-bold">Nama Tengah</label>
                                        <input type="text" class="form-control" id="emp_middle_name" name="emp_middle_name" placeholder="Nama Tengah">
                                    </div>
                                </div>
                                <div class="col-xl-4 col-12">
                                    <div class="form-group">
                                        <label for="emp_last_name" class="font-weight-bold">Nama Belakang</label>
                                        <input type="text" class="form-control" id="emp_last_name" name="emp_last_name" placeholder="Nama Belakang">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_bod" class="font-weight-bold">Tgl. Lahir</label>
                                        <input type="text" class="form-control" id="emp_bod" name="emp_bod" placeholder="Tgl. Lahir" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_birth_place" class="font-weight-bold">Tempat Lahir</label>
                                        <input type="text" class="form-control" id="emp_birth_place" name="emp_birth_place" placeholder="Tempat Lahir">
                                    </div>
                                </div>
                                <div class="col-xl-6 col-12">
                                    <div class="form-group">
                                        <label for="emp_join_date" class="font-weight-bold">Tgl. Bergabung</label>
                                        <input type="text" class="form-control" id="emp_join_date" name="emp_join_date" placeholder="Tgl. Bergabung">
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="form-group">
                                        <label for="emp_work_periode" class="font-weight-bold">Masa Kerja</label>
                                        <span id="emp_work_periode"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-12 col-12"></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>