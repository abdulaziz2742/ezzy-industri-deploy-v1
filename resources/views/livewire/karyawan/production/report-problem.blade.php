<div>

    <!-- Ganti dari reportProblemModal menjadi problemModal -->
    <div class="modal fade" id="problemModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Laporkan Masalah Produksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tipe Masalah</label>
                            <select class="form-select" wire:model="problemType" required>
                                <option value="">Pilih Tipe Masalah</option>
                                <option value="mesin">Masalah Mesin</option>
                                <option value="material">Masalah Material</option>
                                <option value="operator">Masalah Kualitas</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('problemType') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" wire:model="notes" rows="3" required></textarea>
                            @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3" x-data="{ 
                            isUploading: false,
                            handleImageUpload() {
                                const formData = new FormData();
                                const file = $refs.imageInput.files[0];
                                
                                if (!file) {
                                    console.log('No file selected');
                                    alert('Pilih file terlebih dahulu');
                                    return;
                                }

                                console.log('Starting upload process', { fileName: file.name, fileSize: file.size });
                                formData.append('file', file);
                                formData.append('folder', 'problems'); // Add folder parameter
                                this.isUploading = true;
                                
                                fetch('{{ route('upload.image') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    },
                                    body: formData
                                })
                                .then(response => {
                                    console.log('Response received', { status: response.status });
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Upload response', data);
                                    if (data.success) {
                                        console.log('Upload successful', { url: data.url, public_id: data.public_id });
                                        @this.set('cloudinary_url', data.url);
                                        @this.set('cloudinary_id', data.public_id);
                                    } else {
                                        throw new Error(data.error || 'Upload gagal');
                                    }
                                })
                                .catch(error => {
                                    console.error('Upload error:', error);
                                    alert('Upload gagal: ' + error.message);
                                })
                                .finally(() => {
                                    console.log('Upload process completed');
                                    this.isUploading = false;
                                });
                            }
                        }">
                            <label class="form-label">Foto Dokumentasi</label>
                            <input type="file" 
                                   class="form-control" 
                                   x-ref="imageInput"
                                   @change="handleImageUpload()"
                                   accept="image/*">
                            
                            <div x-show="isUploading" class="text-primary mt-1">
                                <small><i class="bi bi-arrow-repeat spinner"></i> Mengupload foto...</small>
                            </div>

                            @if($cloudinary_url)
                                <div class="mt-2">
                                    <img src="{{ $cloudinary_url }}" 
                                         class="img-thumbnail" 
                                         style="max-height: 150px; cursor: pointer;"
                                         onclick="window.open(this.src, '_blank')">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">Simpan</span>
                            <span wire:loading wire:target="save">Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
