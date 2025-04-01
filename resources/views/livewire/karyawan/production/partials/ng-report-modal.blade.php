<div wire:ignore.self class="modal fade" id="ngReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">FORM LAPORAN NG (NON-GOOD / REJECT FORM)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="saveNGReport">
                    <!-- Informasi Dasar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" wire:model="ngReport.date" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama Operator</label>
                                <input type="text" class="form-control" wire:model="ngReport.operator_name" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ID Karyawan</label>
                                <input type="text" class="form-control" wire:model="ngReport.employee_id" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Mesin</label>
                                <input type="text" class="form-control" wire:model="ngReport.machine_name" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shift</label>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" wire:model="ngReport.shift" value="1" id="shift1" readonly>
                                    <label class="btn btn-outline-primary" for="shift1">1</label>
                                    <input type="radio" class="btn-check" wire:model="ngReport.shift" value="2" id="shift2" readonly>
                                    <label class="btn btn-outline-primary" for="shift2">2</label>
                                    <input type="radio" class="btn-check" wire:model="ngReport.shift" value="3" id="shift3" readonly>
                                    <label class="btn btn-outline-primary" for="shift3">3</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Produk -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nomor Lot / Batch</label>
                                <input type="text" class="form-control" wire:model="ngReport.batch_number" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Produk / Part Name</label>
                                <input type="text" class="form-control" wire:model="ngReport.product_name" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Hapus bagian kode produk di sini -->
                            <div class="mb-3">
                                <label class="form-label">Jumlah Produksi</label>
                                <input type="number" class="form-control" wire:model="ngReport.total_production" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah NG</label>
                                <input type="number" class="form-control" wire:model="ngReport.total_ng" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Persentase NG</label>
                                <input type="text" class="form-control" wire:model="ngReport.ng_percentage" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Jenis NG -->
                    <div class="mb-3">
                        <label class="form-label">Jenis NG</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="dimensi" id="ng-dimensi">
                            <label class="form-check-label" for="ng-dimensi">Dimensi Tidak Sesuai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="baret" id="ng-baret">
                            <label class="form-check-label" for="ng-baret">Baret / Lecet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="karat" id="ng-karat">
                            <label class="form-check-label" for="ng-karat">Karat / Korosi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="warna" id="ng-warna">
                            <label class="form-check-label" for="ng-warna">Warna Tidak Sesuai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="las" id="ng-las">
                            <label class="form-check-label" for="ng-las">Cacat Las</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngReport.ng_type" value="lainnya" id="ng-lainnya">
                            <label class="form-check-label" for="ng-lainnya">Lainnya</label>
                        </div>
                        <div class="mt-2" x-show="$wire.ngReport.ng_type === 'lainnya'">
                            <input type="text" class="form-control" wire:model="ngReport.ng_type_other" placeholder="Sebutkan jenis NG lainnya...">
                        </div>
                    </div>

                    <!-- 5W1H -->
                    <div class="mb-3">
                        <label class="form-label">Penyebab NG (5W1H)</label>
                        <div class="mb-2">
                            <label class="form-label">What (Apa masalahnya?)</label>
                            <textarea class="form-control" wire:model="ngReport.what" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Why (Kenapa bisa terjadi?)</label>
                            <textarea class="form-control" wire:model="ngReport.why" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Where (Dimana ditemukan?)</label>
                            <textarea class="form-control" wire:model="ngReport.where" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">When (Kapan ditemukan?)</label>
                            <textarea class="form-control" wire:model="ngReport.when" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Who (Siapa yang menemukan?)</label>
                            <textarea class="form-control" wire:model="ngReport.who" rows="2"></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">How (Bagaimana masalah ini terjadi?)</label>
                            <textarea class="form-control" wire:model="ngReport.how" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Tindakan -->
                    <div class="mb-3">
                        <label class="form-label">Tindakan Perbaikan (Countermeasure)</label>
                        <textarea class="form-control" wire:model="ngReport.countermeasure" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tindakan Pencegahan (Preventive Action)</label>
                        <textarea class="form-control" wire:model="ngReport.preventive_action" rows="3"></textarea>
                    </div>

                    <!-- PIC dan Verifikasi -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">PIC (Person in Charge)</label>
                                <input type="text" class="form-control" wire:model="ngReport.pic">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Diverifikasi oleh (QC/QA)</label>
                                <input type="text" class="form-control" wire:model="ngReport.verified_by">
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <div class="btn-group" role="group">
                            <input type="radio" class="btn-check" wire:model="ngReport.status" value="diperbaiki" id="status1">
                            <label class="btn btn-outline-primary" for="status1">Diperbaiki</label>
                            <input type="radio" class="btn-check" wire:model="ngReport.status" value="scrap" id="status2">
                            <label class="btn btn-outline-primary" for="status2">Scrap</label>
                            <input type="radio" class="btn-check" wire:model="ngReport.status" value="rework" id="status3">
                            <label class="btn btn-outline-primary" for="status3">Rework</label>
                            <input type="radio" class="btn-check" wire:model="ngReport.status" value="pending" id="status4">
                            <label class="btn btn-outline-primary" for="status4">Pending</label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelNGReport">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan & Lanjutkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>