<div class="modal fade" id="modal_hitung_hpp_form">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="no-margins">
                    <label class="no-margins font-weight-bold">Form Hitung HPP</label>
                </h4>
                <button class="close" onclick="closeModal('modal_hitung_hpp_form')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="hpp_form_tour_code" class="no-margins font-weight-bold">Tour Code</label>
                            <input type="text" class="form-control" id="hpp_form_tour_code" readonly placeholder="Tour Code">
                        </div>
                        <div class="form-group">
                            <label for="hpp_form_depature_date" class="no-margins font-weight-bold">Keberangkatan</label>
                            <input type="text" class="form-control" id="hpp_depature_date" readonly placeholder="Tgl. Keberangkatan">
                        </div>
                    </div>
                    <div class="col-sm-6 col-12">
                        <div class="form-group">
                            <label for="hpp_form_tour_leader" class="no-margins font-weight-bold">Pembimbing</label>
                            <input type="text" class="form-control" id="hpp_form_tour_leader" readonly placeholder="Pembimbing">
                        </div>
                        <div class="form-group">
                            <label for="hpp_form_arrival_date" class="no-margins font-weight-bold">Kepulangan</label>
                            <input type="text" class="form-control" id="hpp_form_arrival_date" readonly placeholder="Tgl. Kepulangan">
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-bordered" id="table_detail_hitung_hpp" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">&nbsp;</th>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Kategori</th>
                                        <th class="text-center align-middle">Total Pembayaran (Rp.)</th>
                                        <th class="text-center align-middle">Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" onclick="addRowTable('table_detail_hitung_hpp', '', this.value)" value="1" id="tambah_baris_table_detail_hitung_hpp">Tambah Baris</button>
                <button class="btn btn-primary" onclick="simpanData('modal_hitung_hpp_form')">Simpan Data</button>
            </div>
        </div>
    </div>
</div>