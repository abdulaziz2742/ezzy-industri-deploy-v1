<div wire:ignore.self class="modal fade" id="ngFormModal" tabindex="-1" aria-labelledby="ngFormModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ngFormModalLabel">Form NG</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit="saveNGData">
                    <div class="mb-3">
                        <label class="form-label">Jumlah NG</label>
                        <input type="number" class="form-control" wire:model="ngData.count" min="1">
                        @error('ngData.count') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>  
                    <div class="mb-3">
                        <label class="form-label">Jenis NG</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Dimensi Tidak Sesuai" id="ng-dimensi">
                            <label class="form-check-label" for="ng-dimensi">Dimensi Tidak Sesuai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Baret / Lecet" id="ng-baret">
                            <label class="form-check-label" for="ng-baret">Baret / Lecet</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Karat / Korosi" id="ng-karat">
                            <label class="form-check-label" for="ng-karat">Karat / Korosi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Warna Tidak Sesuai" id="ng-warna">
                            <label class="form-check-label" for="ng-warna">Warna Tidak Sesuai</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Cacat Las" id="ng-las">
                            <label class="form-check-label" for="ng-las">Cacat Las</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" wire:model="ngData.type" value="Lainnya" id="ng-lainnya">
                            <label class="form-check-label" for="ng-lainnya">Lainnya</label>
                        </div>
                        <div class="mt-2" x-show="$wire.ngData.type === 'Lainnya'">
                            <input type="text" class="form-control" wire:model="ngData.typeOther" placeholder="Sebutkan jenis NG lainnya...">
                        </div>
                        @error('ngData.type') <span class="text-danger">{{ $message }}</span> @enderror
                        @error('ngData.typeOther') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" wire:model="ngData.notes"></textarea>
                        @error('ngData.notes') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelNG">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>