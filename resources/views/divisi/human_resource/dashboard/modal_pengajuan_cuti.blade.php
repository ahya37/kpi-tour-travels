{{-- MODAL PENGAJUAN CUTI --}}
<div class="modal fade" id="modal_pgj">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="modal-title no-margins">List Pengajuan</h4>
                    <button class="close" onclick="closeModal('modal_pgj')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-3 col-md-4 col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins">Pilih Bulan</label>
                            <select name="pgj_select_month" id="pgj_select_month" class="form-control" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-borderless" style="width: 100%;" id="table_list_pengajuan">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%;">No</th>
                                        <th class="text-center align-middle" style="width: 15%;">Nama Pengaju</th>
                                        <th class="text-center align-middle" style="width: 20%;">Tgl. Pengajuan</th>
                                        <th class="text-center align-middle">Uraian</th>
                                        <th class="text-center align-middle" style="width: 5%;">Jenis</th>
                                        <th class="text-center align-middle" style="width: 5%;">Status</th>
                                        <th class="text-center align-middle" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PENGAJUAN CUTI KETIKA DITOLAK --}}
<div class="modal fade" id="modal_pgj_tolak">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins modal-title">Konfirmasi Reject </h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold">Tulis Keterangan</label>
                            <input type="hidden" id="pgj_id" class="form-control" placeholder="ID Pengajuan Cuti" readonly>
                            <textarea id="pgj_cuti_note" rows="4" class="form-control" placeholder="Tulis Keterangan"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex flex-row align-items-center justify-content-end">
                <button class="btn btn-danger" type="button" onclick="doSimpan('pengajuan', 'konfirmasi_tolak', '')" title="Tolak Pengajuan">Ya, Tolak</button>
                <button class="btn btn-secondary" type="button" onclick="closeModal('modal_pgj_tolak')" title="Tutup Tampilan">Batal</button>
            </div>
        </div>
    </div>
</div>