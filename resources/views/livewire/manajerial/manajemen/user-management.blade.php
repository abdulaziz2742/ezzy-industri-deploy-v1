<div>
    <div class="pagetitle">
        <h1>Manajemen User</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">User Management</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Form Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $isEditing ? 'Edit User' : 'Tambah User Baru' }}</h5>
                        <form wire:submit.prevent="{{ $isEditing ? 'update' : 'create' }}">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" class="form-control" wire:model="name" required>
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="email" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" wire:model="role" required>
                                    <option value="karyawan">Karyawan</option>
                                    <option value="manajerial">Manajerial</option>
                                </select>
                                @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Departemen</label>
                                <select class="form-select" wire:model="department_id">
                                    <option value="">Pilih Departemen...</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            @if(!$isEditing)
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" wire:model="password" required>
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @endif

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    {{ $isEditing ? 'Update User' : 'Tambah User' }}
                                </button>
                                @if($isEditing)
                                    <button type="button" class="btn btn-secondary mt-2" wire:click="resetForm">
                                        Batal Edit
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Column -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Daftar User</h5>
                        
                        <!-- Search -->
                        <div class="mb-3">
                            <input type="text" class="form-control" wire:model.live="search" placeholder="Cari user...">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
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
                                    @forelse($users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ ucfirst($user->role) }}</td>
                                            <td>{{ $user->department->name ?? '-' }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" wire:click="edit({{ $user->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data user</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>