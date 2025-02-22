<div class="modal fade" id="modal_active_program_umrah_form">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h4 class="modal-title">Modul Program Aktif Umrah - Form</h4>
                <button class="close" title="Tutup Modul" onclick="closeModal('modal_active_program_umrah_form')">&times;</button>
            </div>
            <div class="modal-body"> 
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="act_prog_tour_code">Tour Code</label>
                            <select name="act_prog_tour_code" id="act_prog_tour_code" style="width: 100%;" onchange="showSelectDetail('act_prog_tour_code', this.value)" class="form-control"></select>
                        </div>
                    </div>
                </div>   
                <form id="form_modal_active_program_umrah">
                    <div class="row">
                        <div class="col-xl-6 col-sm-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="act_prog_depature_date">Tgl. Keberangkatan</label>
                                        <input type="text" class="form-control" name="act_prog_depature_date" id="act_prog_depature_date" placeholder="DD/MM/YYYY" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="act_prog_arrival_date">Tgl. Kepulangan</label>
                                        <input type="text" class="form-control" name="act_prog_arrival_date" id="act_prog_arrival_date" placeholder="DD/MM/YYYY" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="act_prog_category">Kategori</label>
                                        <input type="text" class="form-control" name="act_prog_category" id="act_prog_category" placeholder="Kategori" readonly>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="act_prog_program">Program</label>
                                        <input type="text" class="form-control" name="act_prog_program" id="act_prog_program" placeholder="Program" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 d-none">
                            <div class="form-group">
                                <label for="act_prog_uuid">Artikel ID</label>
                                <input type="text" class="form-control" name="act_prog_uuid" id="act_prog_uuid" placeholder="UUID" readonly>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="act_prog_title">Judul Program Umrah</label>
                                <input type="text" class="form-control" name="act_prog_title" id="act_prog_title" placeholder="Judul Artikel">
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_destination">Tujuan</label>
                                <input type="text" class="form-control" name="act_prog_destination" id="act_prog_destination" placeholder="Kota Tujuan">
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_duration">Lamanya</label>
                                <input type="text" class="form-control" name="act_prog_duration" id="act_prog_duration" placeholder="Lamanya">
                                <small>* contoh : 9 Hari 1 Malam</small>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_airlines">Maskapai</label>
                                <input type="text" class="form-control" name="act_prog_airlines" id="act_prog_airlines" placeholder="Maskapai">
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="form-group">
                                <label for="act_prog_hotel_mekkah">Hotel Mekkah</label>
                                {{-- <input type="text" class="form-control" name="act_prog_hotel_mekkah" id="act_prog_hotel_mekkah" placeholder="Hotel Mekkah"> --}}
                                <div class="input-group">
                                    <input type="text" class="form-control" name="act_prog_hotel_mekkah" id="act_prog_hotel_mekkah" placeholder="Hotel Mekkah">
                                    <div class="input-group-append">
                                        <label class="input-group-text font-weight-normal no-margins">/ Setaraf</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="form-group">
                                <label for="act_prog_hotel_madinah">Hotel Madinah</label>
                                {{-- <input type="text" class="form-control" name="act_prog_hotel_madinah" id="act_prog_hotel_madinah" placeholder="Hotel Madinah"> --}}
                                <div class="input-group">
                                    <input type="text" class="form-control" name="act_prog_hotel_madinah" id="act_prog_hotel_madinah" placeholder="Hotel Madinah">
                                    <div class="input-group-append">
                                        <label class="input-group-text no-margins font-weight-normal">/ Setaraf</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_cost_quad">Harga (Quad)</label>
                                <input type="number" min="1" step="1" class="form-control" name="act_prog_cost_quad" id="act_prog_cost_quad" placeholder="Harga Quad (IDR)" value="0" onclick="this.select();">
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_cost_triple">Harga (Triple)</label>
                                <input type="number" min="1" step="1" class="form-control" name="act_prog_cost_triple" id="act_prog_cost_triple" placeholder="Harga Triple (IDR)" value="0" onclick="this.select();">
                            </div>
                        </div>
                        <div class="col-xl-4 col-12">
                            <div class="form-group">
                                <label for="act_prog_cost_double">Harga (Double)</label>
                                <input type="number" min="1" step="1" class="form-control" name="act_prog_cost_double" id="act_prog_cost_double" placeholder="Harga Double (IDR)" value="0" onclick="this.select();">
                            </div>
                        </div>
                        <div class="col-xl-6 col-12">
                            <div class="form-group" id="is_flyer_not_exist">
                                <label for="act_prog_flyer">File : Flyer</label>
                                <input type="file" class="form-input w-100" id="act_prog_flyer" name="act_prog_flyer">
                                <small>* format .jpg, .jpeg, .png</small>
                            </div>
                            <div class="form-group d-none" id="is_flyer_exist">
                                <label for="act_prog_flyer_exist">File : Flyer</label><br>
                                <button type="button" class="btn btn-primary" id="act_prog_flyer_exist" name="act_prog_flyer_exist" value="" onclick="showPhotos(this.value)">Lihat Flyer</button>
                                <button type="button" class="btn btn-success" id="act_prog_flyer_reset" name="act_prog_flyer_reset" onclick="resetForm('form_flyer')" title="Upload Ulang Flyer">
                                    <i class="fa fa-upload"></i> Upload Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <div>
                    <button class="btn btn-danger d-none" id="btn_reject_article_program_umrah" value="reject" onclick="doSaveData('form_modal_active_program_umrah', this.value)" type="button">Tolak</button>
                    <button class="btn btn-success d-none" id="btn_approve_article_program_umrah" value="approve" onclick="doSaveData('form_modal_active_program_umrah', this.value)" type="button">Setujui</button>
                </div>
                <div>
                    <button class="btn btn-secondary" title="Tutup Modul" id="btn_cancel_modal_active_program_umrah_form" onclick="closeModal('modal_active_program_umrah_form')" type="button">Tutup</button>
                    <button class="btn btn-primary" id="btn_act_modal_active_program_umrah_form" value="" onclick="doSaveData('form_modal_active_program_umrah', this.value)" type="button">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</div>