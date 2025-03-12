<div class="modal fade" id="modal_report_pembayaran_haji">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="modal-title no-margins">
                    <label class="no-margins font-weight-bold">Download Laporan Pembayaran Haji</label>
                </h4>
                <button class="close" title="Tutup Tampilan" onclick="closeModal('modal_report_pembayaran_haji')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="">Pilih Tahun</label>
                            <select name="report_pb_hj_year" id="report_pb_hj_year" style="width: 100%;" class="form-control form-select"></select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-primary" onclick="downloadFile('haji', 'excel')"><i class="fa fa-file-excel"></i> Download Laporan</button>
            </div>
        </div>
    </div>
</div>