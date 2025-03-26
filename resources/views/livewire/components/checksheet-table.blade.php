@push('styles')
    <link href="{{ asset('assets/css/custom/pages/checksheet-table.css') }}" rel="stylesheet">
@endpush
<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Checksheet Produksi</h1>
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('karyawan.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('production.start') }}">Mulai Produksi</a></li>
                <li class="breadcrumb-item active">Checksheet</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Daftar Pemeriksaan Mesin</h5>
                    <div style="width: 200px">
                        <h6 class="mb-2">Progress Pemeriksaan</h6>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated" 
                                 role="progressbar" 
                                 @style("width: {$this->progress}%")
                                 aria-valuenow="{{ $this->progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $this->progress }}%
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="width: 5%">NO</th>
                                <th style="width: 20%">NAMA PEMERIKSAAN</th>
                                <th>DESKRIPSI</th>
                                <th style="width: 15%">NILAI STANDAR</th>
                                <th style="width: 20%">STATUS</th>
                                <th style="width: 20%">DOKUMENTASI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tasks as $index => $task)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $task->task_name }}</div>
                                        <span class="badge bg-{{ $task->maintenance_type === 'am' ? 'info' : 'warning' }}">
                                            {{ strtoupper($task->maintenance_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $task->description }}
                                        <div class="mt-2">
                                            <textarea class="form-control form-control-sm" 
                                                    wire:model.live="notes.{{ $task->id }}"
                                                    rows="2"
                                                    placeholder="Tambahkan catatan..."></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->standard_value)
                                            <div class="small">
                                                <strong>Standar:</strong><br>
                                                {{ $task->standard_value }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group w-100" role="group">
                                            <input type="radio" class="btn-check" 
                                                   wire:model.live="checkResults.{{ $task->id }}" 
                                                   id="ok{{ $task->id }}" 
                                                   value="ok">
                                            <label class="btn btn-outline-success btn-sm" for="ok{{ $task->id }}">
                                                <i class="bi bi-check-circle"></i>
                                            </label>

                                            <input type="radio" class="btn-check" 
                                                   wire:model.live="checkResults.{{ $task->id }}" 
                                                   id="not_ok{{ $task->id }}" 
                                                   value="not_ok">
                                            <label class="btn btn-outline-danger btn-sm" for="not_ok{{ $task->id }}">
                                                <i class="bi bi-x-circle"></i>
                                            </label>

                                            <input type="radio" class="btn-check" 
                                                   wire:model.live="checkResults.{{ $task->id }}" 
                                                   id="na{{ $task->id }}" 
                                                   value="na">
                                            <label class="btn btn-outline-secondary btn-sm" for="na{{ $task->id }}">
                                                <i class="bi bi-dash-circle"></i>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @if($task->requires_photo)
                                            <div class="photo-upload-container p-2" x-data="{ 
                                                isUploading: false,
                                                handleImageUpload() {
                                                    const formData = new FormData();
                                                    const file = $refs.imageInput.files[0];
                                                    
                                                    if (!file) {
                                                        alert('Pilih file terlebih dahulu');
                                                        return;
                                                    }
                                                    
                                                    formData.append('file', file);
                                                    formData.append('folder', 'checksheet_tpm'); // Changed folder name
                                                    this.isUploading = true;
                                                    
                                                    fetch('{{ route('upload.image') }}', {
                                                        method: 'POST',
                                                        headers: {
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: formData
                                                    })
                                                    .then(response => response.json())
                                                    .then(data => {
                                                        if (data.success) {
                                                            @this.set('photos.' + {{ $task->id }}, {
                                                                url: data.url,
                                                                public_id: data.public_id
                                                            });
                                                            @this.set('previewUrl.' + {{ $task->id }}, data.url);
                                                        } else {
                                                            throw new Error(data.error || 'Upload gagal');
                                                        }
                                                    })
                                                    .catch(error => {
                                                        console.error('Error:', error);
                                                        alert('Upload gagal: ' + error.message);
                                                    })
                                                    .finally(() => {
                                                        this.isUploading = false;
                                                    });
                                                }
                                            }">
                                                @if(isset($previewUrl[$task->id]))
                                                    <div class="image-preview-wrapper mb-2">
                                                        <img src="{{ $previewUrl[$task->id] }}" 
                                                             class="img-preview"
                                                             alt="Preview"
                                                             onclick="window.open(this.src, '_blank')"
                                                             style="cursor: pointer;">
                                                        <small class="d-block text-muted mt-1">Klik untuk memperbesar</small>
                                                    </div>
                                                @endif
                                                
                                                <div class="upload-wrapper">
                                                    <input type="file" 
                                                           class="form-control form-control-sm" 
                                                           x-ref="imageInput"
                                                           @change="handleImageUpload()"
                                                           accept="image/*">
                                                    
                                                    <div x-show="isUploading" class="upload-loading mt-2">
                                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                        <small class="ms-2">Mengupload foto...</small>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-secondary me-2" wire:click="back">
                            <i class="bi bi-arrow-left me-1"></i>
                            Kembali
                        </button>
                    <button type="button" class="btn btn-primary px-5" 
                            wire:click="startProduction" 
                            {{ count($checkResults) !== count($tasks) ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="startProduction">
                            <i class="bi bi-play-circle me-1"></i>
                            Mulai Produksi
                        </span>
                        <span wire:loading wire:target="startProduction">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <style>
        .photo-upload-container {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: #f8f9fa;
        }
        .img-preview {
            max-width: 100%;
            height: 80px;
            object-fit: contain;
        }
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        .progress-bar {
            background-color: #4154f1;
            transition: width 0.3s ease;
        }
        .table > :not(caption) > * > * {
            padding: 0.75rem;
            vertical-align: middle;
        }
    </style>

    @script
    <script>
        Livewire.on('photo-uploaded', ({ taskId }) => {
            Toast.fire({
                icon: 'success',
                title: 'Foto berhasil diunggah'
            });
        });
    </script>
    @endscript
</div>