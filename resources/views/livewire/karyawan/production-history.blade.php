<div>
    <!-- Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" wire:model.live="startDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" wire:model.live="endDate" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Shift</label>
                    <select wire:model.live="selectedShift" class="form-select">
                        <option value="">Semua Shift</option>
                        <option value="1">Shift 1</option>
                        <option value="2">Shift 2</option>
                        <option value="3">Shift 3</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select wire:model.live="selectedStatus" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="finished">Selesai</option>
                        <option value="running">Berjalan</option>
                        <option value="problem">Bermasalah</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Production History Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Batch</th>
                            <th>Produk</th>
                            <th>Target</th>
                            <th>Realisasi</th>
                            <th>Defect</th>
                            <th>OEE</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productions as $production)
                        <tr>
                            <td>{{ $production->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $production->batch_number }}</td>
                            <td>{{ $production->product }}</td>
                            <td>{{ $production->target_per_shift }}</td>
                            <td>{{ $production->total_production }}</td>
                            <td>{{ $production->defect_count }}</td>
                            <td>{{ optional($production->oeeRecord)->oee_score ?? 0 }}%</td>
                            <td>
                                <span class="badge bg-{{ $production->status === 'finished' ? 'success' : ($production->status === 'problem' ? 'danger' : 'primary') }}">
                                    {{ ucfirst($production->status) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="showDetail({{ $production->id }})" class="btn btn-sm btn-info">
                                    Detail
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
           
        </div>
    </div>

    <!-- Add back the modal section -->
    @if($selectedProduction)
    <div class="modal fade show" tabindex="-1" style="display: block;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Produksi</h5>
                    <button wire:click="closeDetail" type="button" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <!-- Production Details -->
                    <h6>Informasi Produksi</h6>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Batch:</strong> {{ $selectedProduction->batch_number }}</p>
                            <p><strong>Produk:</strong> {{ $selectedProduction->product }}</p>
                            <p><strong>Mesin:</strong> {{ $selectedProduction->machine }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Target:</strong> {{ $selectedProduction->target_per_shift }}</p>
                            <p><strong>Realisasi:</strong> {{ $selectedProduction->total_production }}</p>
                            <p><strong>Defect:</strong> {{ $selectedProduction->defect_count }}</p>
                        </div>
                    </div>

                    <!-- Downtime List -->
                    <h6>Downtime</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Durasi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <!-- Update this section -->
                            <tbody>
                                @foreach($selectedProduction->productionDowntimes as $downtime)
                                <tr>
                                    <td>{{ $downtime->start_time->format('H:i') }} - {{ $downtime->end_time->format('H:i') }}</td>
                                    <td>{{ $downtime->duration_minutes }} menit</td>
                                    <td>{{ $downtime->notes }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Problems List -->
                    <h6>Masalah</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Masalah</th>
                                    <th>Solusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($selectedProduction->problems as $problem)
                                <tr>
                                    <td>{{ $problem->created_at->format('H:i') }}</td>
                                    <td>{{ $problem->description }}</td>
                                    <td>{{ $problem->solution }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- OEE Details -->
                    <h6>OEE Performance</h6>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3>{{ optional($selectedProduction->oeeRecord)->availability_rate ?? 0 }}%</h3>
                                    <small>Availability</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3>{{ optional($selectedProduction->oeeRecord)->performance_rate ?? 0 }}%</h3>
                                    <small>Performance</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3>{{ optional($selectedProduction->oeeRecord)->quality_rate ?? 0 }}%</h3>
                                    <small>Quality</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h3>{{ optional($selectedProduction->oeeRecord)->oee_score ?? 0 }}%</h3>
                                    <small>OEE Score</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeDetail" type="button" class="btn btn-secondary">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>
