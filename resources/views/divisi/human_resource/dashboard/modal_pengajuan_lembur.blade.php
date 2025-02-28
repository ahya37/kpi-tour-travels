{{-- MODAL PENGAJUAN LEMBUR --}}
<div class="modal fade" id="modal_pgj_lmb">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins">List Pengajuan Lemburan</h4>
                    <button type="button" class="close" onclick="closeModal('modal_pgj_lmb')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins" for="select_pgj_month">Pilih Bulan</label>
                            <select id="select_pgj_month" name="select_pgj_month" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-4 col-12">
                        <div class="form-group">
                            <label for="select_pgj_role" class="font-weight-bold no-margins">Pilih Divisi</label>
                            <select name="select_pgj_role" id="select_pgj_role" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped" id="table_pgj_lmb" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">No</th>
                                <th class="text-center align-middle">Nama Pengaju</th>
                                <th class="text-center align-middle">Tgl. Pengajuan</th>
                                <th class="text-center align-middle">Status</th>
                                <th class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PENGAJUAN LEMBUR --}}
<div class="modal fade" id="modal_pgj_lmb_preview">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins">Pengajuan Lembur</h4>
                    <button class="close" type="button" onclick="closeModal('modal_pgj_lmb_preview')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <label>Nama</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="hidden" class="form-control" id="pgj_lmb_act_id" readonly placeholder="Pengajuan Lembur ID">
                        <input type="text" class="form-control" id="pgj_lmb_user_name" readonly placeholder="Nama">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <label>Bagian</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="pgj_lmb_user_division" readonly placeholder="Bagian">
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-4">
                        <label>Untuk</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" id="pgj_lmb_act_desc" readonly placeholder="Untuk">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" style="width: 100%;" id="tbl_pgj_lmb_preview">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tanggal</th>
                                        <th class="text-left align-middle">Uraian Pekerjaan</th>
                                        <th class="text-center align-middle">Jam Mulai</th>
                                        <th class="text-center align-middle">Jam Selesai</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" id="pgj_lmb_btn_tolak" onclick="doSimpan('pengajuan_lembur', 'tolak', '')">Tolak</button>
                <button class="btn btn-primary" id="pgj_lmb_btn_terima" onclick="doSimpan('pengajuan_lembur', 'terima', '')">Konfirmasi</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW PENGAJUAN LEMBUR TOLAK --}}
<div class="modal fade" id="modal_pgj_lmb_preview_tolak">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="modal-title no-margins">
                    Konfirmasi Cancel Lemburan
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <label class="no-margins">Keterangan</label>
                        <textarea class="form-control" id="pgj_lmb_note_tolak" rows="4" placeholder="Tulis Keterangan.."></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex flex-row align-items-center justify-content-end">
                <button class="btn btn-danger" onclick="doSimpan('pengajuan_lembur', 'konfirm_tolak', '')">Ya, Tolak</button>
                <button class="btn btn-primary" onclick="closeModal('modal_pgj_lmb_preview_tolak')">Batal</button>
            </div>
        </div>
    </div>
</div>