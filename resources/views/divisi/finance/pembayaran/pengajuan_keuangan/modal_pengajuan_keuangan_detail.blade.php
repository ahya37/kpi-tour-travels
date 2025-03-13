<div class="modal fade" id="detail_modal_pengajuan_keuangan">
    <div class="modal-dialog modal-xl modal-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="no-margins modal-title">Detail Pengajuan Keuangan </h4>
                <button class="close" onclick="closeModal('detail_modal_pengajuan_keuangan')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6 col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_no_surat">No. Surat Pengajuan</label>
                                    <input type="text" class="form-control" id="pgj_no_surat" readonly placeholder="No. Surat Pengajuan">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_tgl_aju">Tgl. Pengajuan</label>
                                    <input type="text" class="form-control" id="pgj_tgl_aju" readonly placeholder="DD MMM YYYY">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_total_uang">Total Pengajuan</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text font-weight-bold" id="pgj_total_uang_kurs" style="background-color: #fff;"></span>
                                        </div>
                                        <input type="text" class="form-control" id="pgj_total_uang" placeholder="Total Pengajuan" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_file">Bukti Pembayaran</label>
                                    <ul id="pgj_file"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-12">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_deskripsi">Deskripsi Pengajuan</label>
                                    <textarea class="form-control" id="pgj_deskripsi" rows="5" placeholder="Deskripsi Pengajuan" readonly style="resize: none;"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_metode">Metode Pembayaran</label>
                                    <input type="text" class="form-control" id="pgj_metode" readonly placeholder="Metode Pembayaran">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="pgj_no_rekening">Tujuan Pembayaran</label>
                                    <input type="text" class="form-control" id="pgj_no_rekening" readonly placeholder="Tujuan Pembayaran">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h2 class="no-margins font-weight-thin">Transaksi Keuangan</h2>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="row">
                            <div class="col-sm-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins" for="pgj_tr_tour_code">Tour Code</label>
                                    <select name="pgj_tr_tour_code" id="pgj_tr_tour_code" style="width: 100%;" class="form-control form-select"></select>
                                </div>
                            </div>
                            <div class="col-sm-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins" for="pgj_tr_debit">Debit</label>
                                    <select name="pgj_tr_debit" id="pgj_tr_debit" style="width: 100%;" class="form-control form-select"></select>
                                </div>
                                <div class="form-group d-none" id="pgj_tr_debit_amount_view">
                                    <label class="no-margins" for="pgj_tr_debit_amount">Jml. Bayar</label>
                                    <input type="text" class="form-control" name="pgj_tr_debit_amount" id="pgj_tr_debit_amount" placeholder="Jml. Bayar Debit">
                                </div>
                            </div>
                            <div class="col-sm-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins" for="pgj_tr_kredit">Kredit</label>
                                    <select name="pgj_tr_kredit" id="pgj_tr_kredit" style="width: 100%;" class="form-control form-select"></select>
                                </div>
                                <div class="form-group d-none" id="pgj_tr_kredit_amount_view">
                                    <label class="no-margins" for="pgj_tr_kredit_amount">Jml. Bayar</label>
                                    <input type="text" class="form-control" name="pgj_tr_kredit_amount" id="pgj_tr_debit_amount" placeholder="Jml. Bayar Debit">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="table_detail_pengajuan_keuangan">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 8%;">No</th>
                                        <th class="text-center align-middle">Deskripsi</th>
                                        <th class="text-center align-middle" style="width: 10%;">Mata Uang</th>
                                        <th class="text-center align-middle" style="width: 15%;">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th class="text-right align-middle" id="total_pengajuan_keuangan">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('detail_modal_pengajuan_keuangan')" title="Tutup Tampilan">Tutup</button>
                <button class="btn btn-primary" id="btn_modal_pgj_keu" value="">Simpan</button>
            </div>
        </div>
    </div>
</div>