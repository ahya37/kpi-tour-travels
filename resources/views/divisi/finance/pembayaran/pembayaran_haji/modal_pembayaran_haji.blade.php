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
                    {{-- <div class="col-xl-3 col-md-6 col-12">
                        <div class="form-group">
                            <label for="filter_keberangkatan">Estimasi Keberangkatan</label>
                            <select id="filter_keberangkatan" style="form-control" style="width:100%;"></select>
                        </div>
                    </div> --}}
                </div>
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary mb-xl-0 mb-2" title="Tambah Data" onclick="showModal('modal_pembayaran_haji_form', 'add', '')"><i class="fa fa-plus"></i> Tambah Data</button>
                        <button class="btn btn-primary mb-xl-0 mb-2" title="Filter Data" data-toggle="collapse" data-target="#filter_pembayaran_haji"><i class="fa fa-filter"></i> Filter Pencarian</button>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="collapse" id="filter_pembayaran_haji">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-2">
                                        <div class="form-group">
                                            <label for="filter_keberangkatan">Keberangkatan</label>
                                            <select name="filter_keberangkatan" id="filter_keberangkatan" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-2">
                                        <div class="form-group">
                                            <label for="filter_paket">Paket</label>
                                            <select name="filter_paket" id="filter_paket" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                        <th class="text-center align-middle" style="width: 15%;">Paket</th>
                                        <th class="text-center align-middle" style="width: 15%;">Est Berangkat</th>
                                        <th class="text-center align-middle" style="width: 10%;">Status Bayar</th>
                                        <th class="text-center align-middle" style="width: 8%;">Aksi</th>
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