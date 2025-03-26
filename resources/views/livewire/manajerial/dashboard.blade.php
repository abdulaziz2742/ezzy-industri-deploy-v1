<div wire:poll.5s>
    <div class="container-fluid">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Dashboard Manajerial</h1>
            <div>
                <span class="text-muted">Welcome, {{ auth()->user()->name }}</span>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Total Mesin</h6>
                        <h2 class="card-title mb-0">{{ $totalMachines ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Produksi Aktif</h6>
                        <h2 class="card-title mb-0">{{ $activeProductions ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Pending Problems</h6>
                        <h2 class="card-title mb-0">{{ $pendingProblems ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Unresolved Problems</h6>
                        <h2 class="card-title mb-0">{{ $unresolvedProblems ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2">Total Downtime Hari Ini</h6>
                        <h2 class="card-title mb-0">{{ $todayDowntime ?? '0 Jam' }}</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Manajemen Produksi</h5>
                        <p class="card-text">Monitor status produksi dan penanganan masalah.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('manajerial.production.problems') }}" class="btn btn-primary">Lihat Problems</a>
                            <a href="{{ route('manajerial.oee.dashboard') }}" class="btn btn-outline-primary">OEE Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Manajemen Maintenance</h5>
                        <p class="card-text">Monitor dan kelola tugas maintenance dan checksheet.</p>
                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-primary">Lihat Checksheet</a>
                            <a href="#" class="btn btn-outline-primary">Laporan Maintenance</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Data Master</h5>
                        <p class="card-text">Kelola mesin, karyawan, dan data master lainnya.</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('manajerial.machines') }}" class="btn btn-primary">Kelola Mesin</a>
                            <a href="#" class="btn btn-outline-primary">Kelola Pengguna</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>