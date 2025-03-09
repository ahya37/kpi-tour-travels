{{-- MODAL ABSENSI KARYAWAN --}}
<div class="modal fade" id="modal_abs">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex flex-row align-items-center justify-content-between w-100">
                    <h4 class="no-margins modal-title">Table List Absensi</h4>
                    <button class="close" onclick="closeModal('modal_abs')">&times;</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3">
                        <label>Jarak Tanggal</label>
                    </div>
                    <div class="col-sm-3">
                        <label>User</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-3">
                        <input type="text" class="form-control" id="abs_tgl_cari" name="abs_tgl_cari" placeholder="DD/MM/YYYY s/d DD/MM/YYYY" readonly style="background: white; cursor: pointer; height: 38px;">
                    </div>
                    <div class="col-sm-3">
                        <select id="abs_user_cari" name="abs_user_cari" class="form-control" style="width: 100%;"></select>
                    </div>
                    <div class="col-sm-3">
                        <button type="button" class="btn btn-primary" title="Cari Data" style="height: 38px;" onclick="showData('table_list_absensi')">Cari</button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-sm btn-primary" onclick="showData('download_data_excel')" title="Download Absensi Excel">
                            <i class="fa fa-file-excel-o"></i>&nbsp;Download Absensi
                        </button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-striped" style="width: 100%;" id="table_list_absensi">
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" style="width: 10%;">Tanggal</th>
                                    <th class="text-left align-middle">Nama</th>
                                    <th class="text-center align-middle" style="width: 15%;">Jam Masuk</th>
                                    <th class="text-center align-middle" style="width: 15%;">Jam Keluar</th>
                                    <th class="text-center align-middle" style="width: 15%;">Telat Jam</th>
                                    <th class="text-center align-middle" style="width: 15%;">Lebih Jam</th>
                                    <th class="text-center align-middle" style="width: 5%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total : </th>
                                    <th id="table_list_absensi_total_jam_masuk"></th>
                                    <th id="table_list_absensi_total_jam_keluar"></th>
                                    <th id="table_list_absensi_total_jam_telat"></th>
                                    <th id="table_list_absensi_total_jam_lebih"></th>
                                    <th>&nbsp;</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT JAM KERJA ABSEN --}}
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