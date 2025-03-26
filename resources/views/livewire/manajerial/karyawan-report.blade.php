<div>
    <div class="pagetitle">
        <h1>Laporan Kinerja Karyawan</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Laporan Kinerja</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">Filter</h5>
                <div class="btn-group">
                    <button class="btn btn-success me-2" wire:click="exportExcel">
                        <i class="bi bi-file-excel"></i> Export Excel
                    </button>
                    <a href="{{ route('manajerial.karyawan.report.pdf', [
                        'dateFrom' => $dateFrom,
                        'dateTo' => $dateTo,
                        'department' => $selectedDepartment,
                        'status' => $selectedStatus
                    ]) }}" 
                    class="btn btn-danger" 
                    target="_blank">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Cari Karyawan</label>
                    <input type="text" class="form-control" wire:model.live="search" placeholder="Nama karyawan...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" 
                           wire:model.live="dateFrom" 
                           value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" 
                           wire:model.live="dateTo"
                           value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Departemen</label>
                    <select class="form-select" wire:model.live="selectedDepartment">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" wire:model.live="selectedStatus">
                        <option value="">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <!-- Ganti input date dengan select periode -->
                <div class="col-md-3">
                    <label class="form-label">Periode</label>
                    <select class="form-select" wire:model.live="selectedPeriod">
                        <option value="0">Hari Ini</option>
                        <option value="7">7 Hari Terakhir</option>
                        <option value="14">14 Hari Terakhir</option>
                        <option value="30">30 Hari Terakhir</option>
                        <option value="90">3 Bulan Terakhir</option>
                        <option value="180">6 Bulan Terakhir</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th wire:click="sortBy('name')" style="cursor: pointer">
                                Nama Karyawan 
                                @if($sortField === 'name')
                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th>Departemen</th>
                            <th>Quality Check</th>
                            <th>PM/AM Check</th>
                            <th>Produksi</th>
                            <th>Performance</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawan as $k)
                        <tr>
                            <td>{{ $k->name }}</td>
                            <td>{{ $k->department?->name ?? '-' }}</td>
                            <!-- Quality Check Column -->
                            <td>
                                @php
                                    $qcRate = $k->qc_metrics['rate'] ?? 0;
                                @endphp
                                <div>Completed: {{ $k->qc_metrics['completed'] ?? 0 }}/{{ $k->qc_metrics['required'] ?? 0 }}</div>
                                <div>Rate: <span class="badge bg-success">{{ $qcRate }}%</span></div>
                            </td>
                            
                            <td>
                                @php
                                    $maintenanceRate = $k->maintenance_metrics['rate'] ?? 0;
                                @endphp
                                <div>AM: {{ $k->maintenance_metrics['completed_am'] ?? 0 }}</div>
                                <div>PM: {{ $k->maintenance_metrics['completed_pm'] ?? 0 }}</div>
                                <div>Rate: <span class="badge bg-info">{{ $maintenanceRate }}%</span></div>
                            </td>
                            
                            <td>
                                @php
                                    $productionRate = $k->production_metrics['rate'] ?? 0;
                                @endphp
                                <div>Total: {{ $k->production_metrics['total'] ?? 0 }}</div>
                                <div>Rate: <span class="badge bg-primary">{{ $productionRate }}%</span></div>
                                <div>Problems: {{ $k->production_metrics['problems'] ?? 0 }}</div>
                            </td>
                            
                            <td>
                                @php
                                    $performanceRate = $k->performance_rate ?? 0;
                                @endphp
                                @if($performanceRate >= 90)
                                    <span class="badge bg-success">{{ number_format($performanceRate, 1) }}% Excellent</span>
                                @elseif($performanceRate >= 75)
                                    <span class="badge bg-info">{{ number_format($performanceRate, 1) }}% Good</span>
                                @elseif($performanceRate >= 60)
                                    <span class="badge bg-warning">{{ number_format($performanceRate, 1) }}% Fair</span>
                                @else
                                    <span class="badge bg-danger">{{ number_format($performanceRate, 1) }}% Poor</span>
                                @endif
                            </td>
                            <td>    
                                <a href="{{ route('manajerial.karyawan.detail.pdf', [
                                    'userId' => $k->id,
                                    'dateFrom' => $dateFrom,
                                    'dateTo' => $dateTo
                                ]) }}" 
                                class="btn btn-sm btn-danger" 
                                target="_blank">
                                    <i class="bi bi-file-pdf"></i> Detail PDF
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($karyawan->hasPages())
                <div class="mt-3">
                    {{ $karyawan->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal for Detail View -->
    <div class="modal fade" id="detailModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kinerja Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Quality Check Details</h6>
                            <!-- Add detailed QC metrics -->
                        </div>
                        <div class="col-md-6">
                            <h6>Maintenance Check Details</h6>
                            <!-- Add detailed maintenance metrics -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

