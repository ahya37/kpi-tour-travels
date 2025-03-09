<div class="modal fade" id="modal_simulasi_lemburan">
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex w-100 align-items-center justify-content-between">
                <h4 class="no-margins">
                    <label class="no-margins">Simulasi Perhitungan Lembur</label>
                </h4>
                <button class="close" onclick="closeModal('modal_simulasi_lemburan')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-4 col-12">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold">Pilih Karyawan</label>
                            <select name="sml_emp_id" id="sml_emp_id" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="col-xl-4 col-12">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold">Pilih Tanggal</label>
                            <input type="text" class="form-control" name="sml_emp_date" id="sml_emp_date" readonly placeholder="DD/MM/YYYY s/s DD/MM/YYYY" style="background: white; cursor:pointer; height: 38px;">
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label class="no-margins font-weight-bold text-white">Button</label><br>
                            <button type="button" class="btn btn-primary rounded-md" title="Cari Data" style="height: 38px;" onclick="doCari('modal_simulasi_lemburan')">Cari</button>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6 border-right">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Nama</label>
                                    <input type="text" class="form-control form-control-sm" name="sml_emp_name" id="sml_emp_name" placeholder="Nama Karyawan" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Divisi</label>
                                    <input type="text" class="form-control form-control-sm" name="sml_emp_division" id="sml_emp_division" placeholder="Divisi" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-xl-6 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Gaji Pokok</label>
                                    <h2 class="no-margins" name="sml_emp_fee" id="sml_emp_fee">Rp. 0,00</h2>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Pendapatan Per Jam <i class='fa fa-info-circle' style="color: #1ab394; cursor: pointer;" title="Gaji Pokok / 173"></i></label>
                                    <h2 class="no-margins" name="sml_emp_fee_hourly" id="sml_emp_fee_hourly">Rp. 0,00</h2>
                                </div>
                            </div>
                            <div class="col-xl-6 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Total Pendapatan Lembur</label>
                                    <h2 class="no-margins" name="sml_emp_fee_ovt" id="sml_emp_fee_ovt">Rp. 0,00</h2>
                                    <input type="hidden" id="sml_emp_fee_ovt_input">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Total OT1 <i class="fa fa-info-circle" title="(Pendapatan Per Jam * 1.5) * Total OT1" style="color: #1ab394; cursor: pointer;"></i></label>
                                    <h3 class="no-margins" name="sml_emp_ot1" id="sml_emp_ot1">0</h3>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Total OT2 <i class="fa fa-info-circle" title="(Pendapatan Per Jam * 2) * Total OT2" style="color: #1ab394; cursor: pointer;"></i></label>
                                    <h3 class="no-margins" name="sml_emp_ot2" id="sml_emp_ot2">0</h3>
                                </div>
                            </div>
                            <div class="col-xl-4 col-12">
                                <div class="form-group">
                                    <label class="no-margins font-weight-bold">Total OT3 <i class="fa fa-info-circle" title="Pendapatan Per Jam * 3" style="color: #1ab394; cursor: pointer;"></i></label>
                                    <h3 class="no-margins" name="sml_emp_ot3" id="sml_emp_ot3">0</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12">
                        <h2 class="no-margins">Tabel Lemburan</h2><br>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover table-bordered" style="width: 100%;" id="table_emp_ovt">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 5%">No</th>
                                        <th class="text-center align-middle" style="width: 25%">Tanggal Lembur</th>
                                        <th class="text-center align-middle" style="width: 10%">OT 1</th>
                                        <th class="text-center align-middle" style="width: 10%">OT 2</th>
                                        <th class="text-center align-middle" style="width: 10%">OT 3</th>
                                        <th class="text-center align-middle" style="width: 8%">Pengajuan</th>
                                        <th class="text-center align-middle" style="width: 10%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-right">Total :</th>
                                        <th class="text-right" id="table_emp_ovt_total_ot1">0</th>
                                        <th class="text-right" id="table_emp_ovt_total_ot2">0</th>
                                        <th class="text-right" id="table_emp_ovt_total_ot3">0</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" title="Download File" onclick="downloadLemburan()">
                    <i class="fa fa-download"></i> Download File
                </button>
            </div>
        </div>
    </div>
</div>