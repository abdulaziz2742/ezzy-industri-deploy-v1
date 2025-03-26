<div class="row g-3" x-data="{ 
    isUploading: false,
    handleImageUpload() {
        const formData = new FormData();
        const file = $refs.imageInput.files[0];
        
        if (!file) {
            alert('Pilih file terlebih dahulu');
            return;
        }

        console.log('Starting image upload...', {
            fileName: file.name,
            fileSize: file.size,
            fileType: file.type
        });

        formData.append('file', file);
        this.isUploading = true;
        
        fetch('{{ route('upload.image') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(async response => {
            console.log('Server response status:', response.status);
            const data = await response.json();
            console.log('Server response:', data);
            
            if (!response.ok) {
                throw new Error(data.message || 'Server error');
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                console.log('Upload successful:', {
                    url: data.url,
                    public_id: data.public_id
                });
                @this.set('cloudinary_url', data.url);
                @this.set('cloudinary_id', data.public_id);
            } else {
                throw new Error(data.error || 'Terjadi kesalahan saat upload');
            }
        })
        .catch(error => {
            console.error('Upload failed:', error);
            alert('Upload gagal: ' + error.message);
        })
        .finally(() => {
            console.log('Upload process completed');
            this.isUploading = false;
        });
    }
}">
    <div class="col-md-6">
        <label class="form-label">Parameter Name</label>
        <input type="text" class="form-control" wire:model="judul">
        @error('judul') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Step Order</label>
        <input type="number" class="form-control" wire:model="urutan">
        @error('urutan') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea class="form-control" wire:model="deskripsi" rows="3"></textarea>
        @error('deskripsi') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Measurement Type</label>
        <select class="form-select" wire:model.live="measurement_type">
            <option value="">Select Type</option>
            <option value="length">Length</option>
            <option value="diameter">Diameter</option>
            <option value="weight">Weight</option>
            <option value="temperature">Temperature</option>
            <option value="pressure">Pressure</option>
            <option value="angle">Angle</option>
            <option value="time">Time</option>
            <option value="other">Other</option>
        </select>
        @error('measurement_type') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Measurement Unit</label>
        <select class="form-select" wire:model="measurement_unit">
            <option value="">Select Unit</option>
            @if($measurement_type === 'length')
                <option value="mm">Millimeter (mm)</option>
                <option value="cm">Centimeter (cm)</option>
                <option value="m">Meter (m)</option>
            @elseif($measurement_type === 'diameter')
                <option value="mm">Millimeter (mm)</option>
                <option value="cm">Centimeter (cm)</option>
            @elseif($measurement_type === 'weight')
                <option value="g">Gram (g)</option>
                <option value="kg">Kilogram (kg)</option>
            @elseif($measurement_type === 'temperature')
                <option value="°C">Celsius (°C)</option>
                <option value="°F">Fahrenheit (°F)</option>
            @elseif($measurement_type === 'pressure')
                <option value="Bar">Bar</option>
                <option value="PSI">PSI</option>
            @elseif($measurement_type === 'angle')
                <option value="degree">Degree (°)</option>
            @elseif($measurement_type === 'time')
                <option value="s">Second (s)</option>
                <option value="min">Minute (min)</option>
                <option value="hour">Hour</option>
            @elseif($measurement_type === 'other')
                <option value="unit">Unit</option>
            @endif
        </select>
        @error('measurement_unit') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Standard Value</label>
        <input type="text" class="form-control" wire:model="nilai_standar">
        @error('nilai_standar') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Min Tolerance</label>
        <input type="text" class="form-control" wire:model="toleransi_min">
        @error('toleransi_min') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Max Tolerance</label>
        <input type="text" class="form-control" wire:model="toleransi_max">
        @error('toleransi_max') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Check Interval</label>
        <div class="input-group">
            <input type="number" class="form-control" wire:model="interval_value" placeholder="Value">
            <select class="form-select" wire:model="interval_unit">
                <option value="">Select Unit</option>
                <option value="pcs">Pieces</option>
                <option value="set">Set</option>
                <option value="box">Box</option>
                <option value="batch">Batch</option>
                <option value="hour">Hour</option>
                <option value="shift">Shift</option>
            </select>
        </div>
        @error('interval_value') <span class="text-danger">{{ $message }}</span> @enderror
        @error('interval_unit') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Image (Optional)</label>
        <input type="file" 
               class="form-control" 
               x-ref="imageInput"
               @change="handleImageUpload()"
               accept="image/*">
        
        <div x-show="isUploading">
            <div class="spinner-border spinner-border-sm text-primary mt-2" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <small class="text-muted ms-2">Uploading image...</small>
        </div>

        @if($cloudinary_url)
            <div class="mt-2 position-relative">
                <img src="{{ $cloudinary_url }}" 
                     class="img-thumbnail" 
                     style="max-height: 200px">
                <button type="button" 
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                        wire:click="removeImage">
                    <i class="bi bi-x"></i>
                </button>
            </div>
        @endif
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" 
                    wire:click="closeModal">
                Cancel
            </button>
            <button type="button" class="btn btn-primary" 
                    wire:click="{{ $isEditing ? 'update' : 'store' }}">
                {{ $isEditing ? 'Update' : 'Save' }}
            </button>
        </div>
    </div>
</div>