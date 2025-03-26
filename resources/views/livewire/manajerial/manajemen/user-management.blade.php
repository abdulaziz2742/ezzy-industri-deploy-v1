<div>
    <div class="pagetitle">
        <h1>Manajemen User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Manajemen User</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ $isEditing ? 'Edit User' : 'Tambah User Baru' }}</h5>

            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif

            <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" wire:model="email">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select class="form-select" wire:model="role">
                            <option value="karyawan">Karyawan</option>
                            <option value="manajerial">Manajerial</option>
                        </select>
                        @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Departemen</label>
                        <select class="form-select" wire:model="department_id">
                            <option value="">Pilih Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                @if(!$isEditing)
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" wire:model="password">
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                @endif
                </div>

                <div class="text-end">
                    @if($isEditing)
                        <button type="button" class="btn btn-secondary" wire:click="cancel">Batal</button>
                    @endif
                    <button type="submit" class="btn btn-primary">{{ $isEditing ? 'Update' : 'Simpan' }}</button>
                </div>
            </form>

            <hr>

            <div class="row mb-3">
                <div class="col">
                    <input type="text" class="form-control" placeholder="Cari user..." wire:model.live="search">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Departemen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ ucfirst($user->role) }}</td>
                                <td>{{ $user->department->name ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-sm btn-info" wire:click="edit({{ $user->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" wire:click="delete({{ $user->id }})" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        </div>
    </div>
</div>