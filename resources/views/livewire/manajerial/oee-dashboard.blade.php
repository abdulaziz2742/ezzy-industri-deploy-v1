<div wire:poll.{{ $refreshInterval }}ms>
    <div class="pagetitle">
        <h1>OEE Dashboard</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">OEE Dashboard</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <!-- Existing filters -->
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" wire:model.live="startDate">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" wire:model.live="endDate">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Shift</label>
                    <select class="form-select" wire:model.live="selectedShift">
                        <option value="">Semua Shift</option>
                        @foreach($shifts as $shift)
                            <option value="{{ $shift->id }}">{{ $shift->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('manajerial.oee.dashboard.pdf') }}" 
                       class="btn btn-danger d-block" 
                       target="_blank">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-info d-block" data-bs-toggle="modal" data-bs-target="#oeeGuideModal">
                        <i class="bi bi-question-circle"></i> Panduan OEE
                    </button>
                </div>
            </div>

            <!-- Existing table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Mesin</th>
                            <th>Shift</th>
                            <th>Availability</th>
                            <th>Performance</th>
                            <th>Quality</th>
                            <th>OEE Score</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($oeeRecords as $record)
                            <tr>
                                <td>{{ $record->machine->name }}</td>
                                <td>{{ $record->shift->name }}</td>
                                <td>{{ number_format($record->availability_rate, 2) }}%</td>
                                <td>{{ number_format($record->performance_rate, 2) }}%</td>
                                <td>{{ number_format($record->quality_rate, 2) }}%</td>
                                <td>{{ number_format($record->oee_score, 2) }}%</td>
                                <td>
                                    <a href="{{ route('manajerial.oee.detail', $record->machine_id) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- OEE Guide Modal -->
    <div class="modal fade" id="oeeGuideModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Panduan Overall Equipment Effectiveness (OEE)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-bold">Apa itu OEE?</h6>
                    <p>Overall Equipment Effectiveness (OEE) adalah metrik standar untuk mengukur efektivitas manufaktur. OEE mengidentifikasi persentase waktu manufaktur yang benar-benar produktif.</p>

                    <h6 class="fw-bold mt-4">Komponen OEE</h6>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-primary">1. Availability (Ketersediaan)</h6>
                            <p>Mengukur persentase waktu yang direncanakan peralatan benar-benar beroperasi.</p>
                            <code>Availability = (Operating Time / Planned Production Time) × 100%</code>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-success">2. Performance (Kinerja)</h6>
                            <p>Mengukur kecepatan aktual operasi sebagai persentase dari kecepatan ideal.</p>
                            <code>Performance = ((Total Output × Ideal Cycle Time) / Operating Time) × 100%</code>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="text-warning">3. Quality (Kualitas)</h6>
                            <p>Mengukur produk baik yang diproduksi sebagai persentase dari total produk yang diproduksi.</p>
                            <code>Quality = (Good Output / Total Output) × 100%</code>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4">Perhitungan OEE Score</h6>
                    <div class="card">
                        <div class="card-body">
                            <p>OEE Score adalah hasil perkalian dari ketiga komponen di atas:</p>
                            <code>OEE = (Availability × Performance × Quality) / 10000</code>
                            
                            <div class="mt-3">
                                <p class="mb-2">Standar World Class OEE:</p>
                                <ul>
                                    <li>OEE Score: 85% atau lebih</li>
                                    <li>Availability: 90% atau lebih</li>
                                    <li>Performance: 95% atau lebih</li>
                                    <li>Quality: 99.9% atau lebih</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>