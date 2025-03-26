<div>
    <div class="pagetitle">
        <h1>Master Produk</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Master Produk</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h5>
                        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kode Produk</label>
                                    <input type="text" class="form-control" wire:model="code">
                                    @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" wire:model="name">
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Target Per Jam</label>
                                    <input type="number" class="form-control" wire:model="target_per_hour">
                                    @error('target_per_hour') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Target Per Shift</label>
                                    <input type="number" class="form-control" wire:model="target_per_shift">
                                    @error('target_per_shift') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Target Per Hari</label>
                                    <input type="number" class="form-control" wire:model="target_per_day">
                                    @error('target_per_day') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Add new row for cycle time -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cycle Time (menit/unit)</label>
                                    <input type="number" 
                                           step="0.01" 
                                           min="0.01" 
                                           placeholder="Contoh: 1.50"
                                           class="form-control" 
                                           wire:model="cycle_time">
                                    <small class="text-muted">Gunakan titik (.) untuk desimal. Contoh: 1.5 menit</small>
                                    @error('cycle_time') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Satuan</label>
                                    <select class="form-select" wire:model="unit">
                                        <option value="">Pilih Satuan...</option>
                                        <option value="PCS">PCS</option>
                                        <option value="SET">SET</option>
                                        <option value="LEMBAR">LEMBAR</option>
                                        <option value="BATANG">BATANG</option>
                                        <option value="ROLL">ROLL</option>
                                        <option value="METER">METER</option>
                                        <option value="KG">KG</option>
                                    </select>
                                    @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control" wire:model="description" rows="2"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="text-end">
                                @if($isEditing)
                                    <button type="button" class="btn btn-secondary btn-sm me-2" wire:click="cancelEdit">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </button>
                                @endif
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-save"></i> {{ $isEditing ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar Produk</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Kode</th>
                                        <th>Satuan</th>
                                        <th>Cycle Time</th>
                                        <th>Target/Jam</th>
                                        <th>Target/Shift</th>
                                        <th>Target/Hari</th>
                                        <th>Deskripsi</th>
                                        <th>Dibuat Pada</th>
                                        <th>Diupdate Pada</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($products as $index => $product)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->code }}</td>
                                            <td>{{ $product->unit }}</td>
                                            <td>{{ number_format($product->cycle_time, 2, '.', ',') }} menit</td>
                                            <td>{{ $product->target_per_hour ?? '-' }}</td>
                                            <td>{{ $product->target_per_shift ?? '-' }}</td>
                                            <td>{{ $product->target_per_day ?? '-' }}</td>
                                            <td>{{ Str::limit($product->description, 30) }}</td>
                                            <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-warning" wire:click="edit({{ $product->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" wire:click="delete({{ $product->id }})"
                                                    onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">Belum ada data produk</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>