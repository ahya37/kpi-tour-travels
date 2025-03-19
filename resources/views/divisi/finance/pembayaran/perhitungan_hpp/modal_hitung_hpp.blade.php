<div class="modal fade" id="modal_hitung_hpp">
    <div class="modal-dialog modal-dialog-scrollabel modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between w-100">
                <h4 class="no-margins">
                    <label class="no-margins font-weight-bold">Perhitungan HPP</label>
                </h4>
                <button class="close" onclick="closeModal('modal_hitung_hpp')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-primary" title="Filter Data" data-toggle="collapse" data-target="#filter_data_tour_code"><i class="fa fa-filter"></i> Filter Data</button>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="collapse" id="filter_data_tour_code">
                            <div class="card card-body">
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-2">
                                        <label for="hpp_filter_keberangkatan">Keberangkatan</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-2">
                                        <select name="hpp_filter_keberangkatan" id="hpp_filter_keberangkatan" style="width: 100%;" onchange="showSelectDetail(this.id, this.value)"></select>
                                    </div>
                                    <div class="col-xl-3 col-md-6 col-12 mb-xl-0 mb-2">
                                        <button class="btn btn-primary" style="height: 38px;" tilte="Download Report" disabled id="hpp_download_excel" onclick="downloadFile('hpp')"><i class="fa fa-file-excel"></i> Download File</button>
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
                            <table class="table table-striped table-hover table-bordered" id="table_list_hpp" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">No</th>
                                        <th class="text-center align-middle">Tour Code</th>
                                        <th class="text-center align-middle">Keberangkatan</th>
                                        <th class="text-center align-middle">Kepulangan</th>
                                        <th class="text-center align-middle">Pembimbing</th>
                                        <th class="text-center align-middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>