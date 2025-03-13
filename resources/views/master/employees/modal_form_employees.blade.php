<div class="modal fade" id="modal_form_employees">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="modal-title no-margins font-weight-bold">Tambah Data Employee</h4>
                <button class="close" onclick="close_modal('modal_form_employees')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-row mb-2 d-none">
                    <div class="col-sm-4">
                        <h4>ID Employee</h4>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" placeholder="ID Employee" id="employee_id" readonly>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col-sm-4">
                        <h4>Nama Lengkap</h4>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control form-control-sm" placeholder="Nama Lengkap" id="employee_name" autocomplete="off" onkeyup="generateEmailUser(this.value)">
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col-sm-4">
                        <h4>Group Divisi</h4>
                    </div>
                    <div class="col-sm-8">
                        <select id="employee_group_division" class="form-control form-control-sm" style="width: 100%;"></select>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col-sm-4">
                        <h4>Role User</h4>
                    </div>
                    <div class="col-sm-8">
                        <select id="employee_role" class="form-control form-control-sm" style="width: 100%;"></select>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col-sm-4">
                        <h4>Email</h4>
                    </div>
                    <div class="col-sm-8">
                        <input id="employee_username" type="text" class="form-control form-control-sm" placeholder="Otomatis Terisi" readonly style="height: 37.5px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="close_modal('modal_form_employees')">Batal</button>
                <button type="button" class="btn btn-primary" id="btn_simpan_form_employee" onclick="do_simpan(this.value)" value="">Simpan</button>
            </div>
        </div>
    </div>
</div>