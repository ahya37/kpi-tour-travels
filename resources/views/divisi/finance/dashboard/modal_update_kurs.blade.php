<div class="modal fade" id="modal_update_kurs">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title no-margins">Update Kurs Harian</h4>
                <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_update_kurs')">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kurs_id">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="kurs_start_date">Berlaku Mulai</label>
                            <input type="text" class="form-control" id="kurs_start_date" readonly placeholder="DD/MM/YYYY">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="kurs_value_high">Kurs Tertinggi</label>
                            <input type="text" class="form-control" id="kurs_value_high" value="0" onclick="this.select()">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="kurs_value_low">Kurs Terendah</label>
                            <input type="text" class="form-control" id="kurs_value_low" value="0" onclick="this.select()">
                        </div>
                    </div>
                    <div class="col-12 text-right">
                        <button class="btn btn-sm btn-danger">Reset</button>
                        <button class="btn btn-sm btn-primary" onclick="doUpdate('modal_update_kurs', this.value, '')" id="btn_simpan_kurs">Simpan</button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-hover" style="width: 100%;" id="table_list_kurs">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tanggal</th>
                                        <th class="text-center align-middle">Terendah</th>
                                        <th class="text-center align-middle">Tertinggi</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center"><i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>