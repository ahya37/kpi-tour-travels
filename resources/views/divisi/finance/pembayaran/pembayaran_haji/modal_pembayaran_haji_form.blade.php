<div class="modal fade" id="modal_pembayaran_haji_form">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="no-margins">
                    <label class="no-margins font-weight-bold">Form Pembayaran Haji</label>
                </h4>
                <button class="close" onclick="closeModal('modal_pembayaran_haji_form')" title="Tutup Tampilan">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6 col-md-12">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold" for="hj_member_id">
                                <h4 class="no-margins">Nama Jemaah</h4>
                            </label>
                            <select id="hj_member_id" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, this.value)"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="form-group">
                                    <label for="hj_depature_code">Kode Keberangkatan</label>
                                    <select id="hj_depature_code" style="width: 100%;" class="form-control" onchange="showSelectDetail(this.id, this.value)"></select>
                                </div>
                                <div class="form-group">
                                    <label for="hj_no_daftar">No. Daftar</label>
                                    <input type="text" class="form-control" id="hj_no_daftar" readonly placeholder="No. Daftar">
                                </div>
                                <div class="form-group">
                                    <label for="hj_tgl_daftar">Tgl. Daftar</label>
                                    <input type="text" id="hj_tgl_daftar" class="form-control" readonly placeholder="Tgl. Daftar">
                                </div>
                                <div class="form-group">
                                    <label for="hj_no_bpih">No. BPIH</label>
                                    <input type="text" id="hj_no_bpih" class="form-control" readonly placeholder="No. BPIH">
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="form-group">
                                    <label for="hj_room">Paket</label>
                                    <input type="text" id="hj_room" class="form-control" readonly placeholder="Paket">
                                </div>
                                <div class="form-group">
                                    <label for="hj_room_price">Harga Paket / Pax</label>
                                    <input type="text" id="hj_room_price" class="form-control" readonly placeholder="Harga Paket / Pax">
                                </div>
                                <div class="form-group">
                                    <label for="hj_current_payment">Total Pembayaran</label>
                                    <input type="text" id="hj_current_payment" class="form-control" readonly placeholder="Total Pembayaran">
                                </div>
                                <div class="form-group">
                                    <label for="hj_status_payment">Status Pembayaran</label>
                                    <input type="text" id="hj_status_payment" class="form-control" placeholder="Status Pembayaran" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hovered table-borderd" id="table_pembayaran_haji_form" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">&nbsp;</th>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tgl. Bayar</th>
                                        <th class="text-center align-middle">Metode</th>
                                        <th class="text-center align-middle">No. Rekening</th>
                                        <th class="text-center align-middle">Mata Uang</th>
                                        <th class="text-center align-middle">Jml. Bayar</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="btn_tambah_baris_haji" value="1" onclick="addRowTable('table_pembayaran_haji_form', [], this.value)"><i class="fa fa-plus"></i> Tambah Baris</button>
                |
                {{-- <button class="btn btn-secondary" onclick="closeModal('modal_pembayaran_haji_form')" title="Tutup Tampilan">Tutup</button> --}}
                <button class="btn btn-primary" id="btn_simpan_pembayaran_haji" value="" title="Simpan Data" onclick="simpanData('modal_pembayaran_haji', this.value, [])">Simpan</button>
            </div>
        </div>
    </div>
</div>