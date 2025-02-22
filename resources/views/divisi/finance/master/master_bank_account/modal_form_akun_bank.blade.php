<div class="modal fade" id="modal_form_account_bank">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title no-margins">
                    <label class="no-margins">Form Akun Bank</label>
                </h4>
                <button class="close" onclick="closeModal('modal_form_account_bank')">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="acb_id" id="acb_id">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins" for="acb_bank_id">Bank</label>
                            <select name="acb_bank_id" id="acb_bank_id" class="form-control" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins" for="acb_coa_id">Kode CoA</label>
                            <select name="acb_coa_id" id="acb_coa_id" class="form-control" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins" for="acb_currency">Mata Uang</label>
                            <select name="acb_currency" id="acb_currency" class="form-control" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="font-weight-bold no-margins" for="acb_bank_account">No. Rekening</label>
                            <input type="text" class="form-control" name="acb_bank_account" id="acb_bank_account" placeholder="No. Rekening">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('modal_form_account_bank')" title="Tutup Tampilan">Tutup</button>
                <button class="btn btn-primary" onclick="doSimpan('modal_form_account_bank', this.value, '')" title="Simpan Data" value="" id="btnSimpanFormBankAccount">Simpan</button>
            </div>
        </div>
    </div>
</div>