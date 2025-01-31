<div class="modal fade" id="modal_edit_jam_kerja">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="modal-title">Edit Jam Kerja</h4>
                <button class="close" onclick="closeModal('modal_edit_jam_kerja')" title="Tutup Tampilan">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="edit_jam_kerja_nama">Nama</label>
                            <input type="hidden" class="form-control" id="edit_jam_kerja_user_id" placeholder="User ID" readonly>
                            <input type="text" class="form-control" id="edit_jam_kerja_nama" placeholder="Nama Lengkap" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="edit_jam_kerja_tanggal">Tanggal</label>
                            <input type="text" class="form-control" id="edit_jam_kerja_tanggal" placeholder="DD/MM/YYYY" readonly>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="edit_jam_kerja_jam_masuk">Jam Masuk</label>
                            <input type="text" class="form-control" id="edit_jam_kerja_jam_masuk" placeholder="Jam Masuk" onclick="this.select()">
                            <small>* Format : Jam:Menit (HH:mm) </small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="edit_jam_kerja_jam_keluar">Jam Keluar</label>
                            <input type="text" class="form-control" id="edit_jam_kerja_jam_keluar" placeholder="Jam Keluar" onclick="this.select()">
                            <span>* Format : Jam:Menit (HH:mm) </span>
                        </div>  
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" title="Tutup Tampilan" onclick="closeModal('modal_edit_jam_kerja')">Tutup</button>
                <button class="btn btn-primary" title="Simpan" onclick="doSimpan('edit_jam_kerja', 'edit', '')">Simpan</button>
            </div>
        </div>
    </div>
</div>