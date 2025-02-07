<div class="modal fade" id="modal_active_program_umrah">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="modal-title">Modul Program Aktif Umrah</h4>
                <button class="close" title="Tutup Modul" onclick="closeModal('modal_active_program_umrah')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                        <button class="btn btn-primary" onclick="showModal('modal_active_program_umrah_form', '', 'add')" title="Tambah Data">Tambah</button>
                    </div>
                </div>
                <hr>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="table_active_program_umrah" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="text-center align-middle">No</th>
                                <th class="text-center align-middle">Tour Code</th>
                                <th class="text-center align-middle">Title</th>
                                <th class="text-center align-middle">Program</th>
                                <th class="text-center align-middle">Status</th>
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