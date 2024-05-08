<div class="modal inmodal fade" id="myModal5" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Tambah Target</h4>
            </div>
            <div class="modal-body">
                <div id="loading"></div>
                {{-- <form id="form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group  row"><label class="col-sm-2 col-form-label">Tahun</label>
                        <div class="col-sm-10"><input id="year" type="text" name="year"
                                class="form-control" required></div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-white" data-dismiss="modal"
                            onclick="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary ladda-button" id="saveButton">Simpan</button>
                    </div>
                    </form> --}}
                    
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-white" data-dismiss="modal"
                    onclick="closeModal()">Batal</button>
                <button type="button" class="btn btn-sm btn-primary ladda-button" id="saveButton">Simpan</button>
            </div>


        </div>
    </div>
</div>