<div class="modal fade" id="modal_pembayaran_haji">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="no-margins">
                    <label class="no-margins font-weight-bold">Pembayaran Haji {{ date('Y') }}</label>
                </h4>
                <button class="close" onclick="closeModal('modal_pembayaran_haji')" title="Tutup Tampilan">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-3 col-6">
                        <button class="btn btn-primary" title="Tambah Data" onclick="showModal('modal_pembayaran_haji_form')"><i class="fa fa-plus"></i> Tambah Data</button>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered table-hovered" id="table_pembayaran_haji" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle" style="width: 8%;">No</th>
                                        <th class="text-center align-middle">Nama</th>
                                        <th class="text-center align-middle" style="width: 15%;">Tgl. Daftar</th>
                                        <th class="text-center align-middle" style="width: 15%;">Paket</th>
                                        <th class="text-center align-middle" style="width: 10%;">Status Bayar</th>
                                        <th class="text-center align-middle" style="width: 10%;">Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>