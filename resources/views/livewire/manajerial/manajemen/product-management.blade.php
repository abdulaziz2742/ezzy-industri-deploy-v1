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

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $isEditing ? 'Edit Produk' : 'Tambah Produk Baru' }}</h5>

            @if (session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Produk</label>
                        <input type="text" class="form-control" wire:model="code">
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kode Internal</label>
                        <input type="text" class="form-control" wire:model="product_code">
                        @error('product_code') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Target Per Jam</label>
                        <input type="number" class="form-control" wire:model="target_per_hour">
                        @error('target_per_hour') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Target Per Shift</label>
                        <input type="number" class="form-control" wire:model="target_per_shift">
                        @error('target_per_shift') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Target Per Hari</label>
                        <input type="number" class="form-control" wire:model="target_per_day">
                        @error('target_per_day') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Cycle Time (menit)</label>
                        <input type="number" step="0.01" class="form-control" wire:model="cycle_time">
                        @error('cycle_time') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Satuan</label>
                        <input type="text" class="form-control" wire:model="unit">
                        @error('unit') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="text-end">
                    @if($isEditing)
                        <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Batal</button>
                    @endif
                    <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Simpan' }}</button>
                </div>
            </form>

            <hr>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Kode</th>
                            <th>Kode Internal</th>
                            <th>Satuan</th>
                            <th>Cycle Time</th>
                            <th>Target/Jam</th>
                            <th>Target/Shift</th>
                            <th>Target/Hari</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->code }}</td>
                                <td>{{ $product->product_code }}</td>
                                <td>{{ $product->unit }}</td>
                                <td>{{ number_format($product->cycle_time, 2) }} menit</td>
                                <td>{{ $product->target_per_hour }}</td>
                                <td>{{ $product->target_per_shift }}</td>
                                <td>{{ $product->target_per_day }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" wire:click="edit({{ $product->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" 
                                        wire:click="delete({{ $product->id }})"
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