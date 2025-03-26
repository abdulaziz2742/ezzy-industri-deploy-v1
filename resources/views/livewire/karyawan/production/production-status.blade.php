<div> <!-- Root div -->
@if($activeProduction)
    @livewire('karyawan.production.report-problem', ['productionId' => $activeProduction->id], key('report-problem-' . $activeProduction->id))
@endif

@push('styles')
        <link href="{{ asset('assets/css/custom/pages/production-status.css') }}" rel="stylesheet">
        <style>
            .production-info-card {
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .production-info-card .card-title {
                color: #2c384e;
                font-weight: 600;
                border-bottom: 2px solid #eef2f5;
                padding-bottom: 10px;
                margin-bottom: 20px;            
            }
            .info-item {
                padding: 8px 0;
                border-bottom: 1px solid #f0f0f0;
            }
            .info-item:last-child {
                border-bottom: none;
            }
            .info-label {
                color: #6c757d;
                font-weight: 500;
            }
            .info-value {
                font-weight: 500;
                text-align: right;
            }
            .status-badge {
                padding: 8px 15px;
                border-radius: 20px;
                font-weight: 500;
            }
        </style>
    @endpush

    <div>
        <!-- Page Title Section -->
        <div class="pagetitle bg-light p-3 rounded mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">Status Produksi</h1>
                    <nav class="mt-2">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('karyawan.dashboard') }}">Beranda</a></li>
                            <li class="breadcrumb-item active">Status Produksi</li>
                        </ol>
                    </nav>
                </div>
                @if(!$activeProduction)
                    <a href="{{ route('production.start') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Mulai Produksi Baru
                    </a>
                @endif
            </div>
        </div>

        <section class="section" wire:poll.5s>
            @if(!$activeProduction)
                <div class="text-center py-5">
                    <img src="{{ asset('assets/img/not-found.svg') }}" alt="No Production" class="img-fluid mb-4" style="max-width: 250px">
                    <h4 class="text-secondary">Belum Ada Produksi Aktif</h4>
                    <p class="text-muted">Silakan mulai produksi baru untuk memulai</p>
                </div>
            @else
                <div class="row g-4">
                    <!-- Production Info Card -->
                    <div class="col-lg-4">
                        <div class="card production-info-card h-100">
                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Informasi Produksi
                                </h5>
                                <div class="info-items">
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">Mesin</span>
                                        <span class="info-value">{{ $activeProduction->machine }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">Produk</span>
                                        <span class="info-value">{{ $activeProduction->product }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">SOP Number</span>
                                        <span class="info-value">SOP-QCD{{ $activeProduction->product }}-{{ $activeProduction->start_time->format('dmY') }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">Shift</span>
                                        <span class="info-value">{{ $activeProduction->shift->name }}</span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">Status</span>
                                        <span class="status-badge badge bg-{{ 
                                            $activeProduction->status === 'running' ? 'success' : 
                                            ($activeProduction->status === 'problem' ? 'danger' : 
                                            ($activeProduction->status === 'paused' ? 'warning' : 'primary')) 
                                        }}">
                                            {{ ucfirst($activeProduction->status) }}
                                            @if($activeProduction->status === 'problem')
                                                @php
                                                    $problem = $activeProduction->problems()->latest()->first();
                                                @endphp
                                                @if($problem && $problem->status === 'pending')
                                                    <small>(Menunggu Approval)</small>
                                                @endif
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item d-flex justify-content-between align-items-center">
                                        <span class="info-label">Mulai</span>
                                        <span class="info-value">{{ $activeProduction->start_time->setTimezone('Asia/Jakarta')->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tambahkan setelah card Production Info, sebelum Control and History Column -->
                    <div class="card production-info-card mt-4">
                        <div class="card-body">
                            <h5 class="card-title d-flex align-items-center">
                                <i class="bi bi-graph-up me-2"></i>
                                Quality Check Progress
                            </h5>
                            
                            <div class="quality-progress mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>Progress Quality Check</span>
                                    <span class="fw-bold">{{ $checkProgress }}%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ 
                                        $checkProgress < 50 ? 'bg-danger' : 
                                        ($checkProgress < 80 ? 'bg-warning' : 'bg-success') 
                                    }}" 
                                    role="progressbar" 
                                    @style(['width' => $checkProgress.'%'])
                                    aria-valuenow="{{ $checkProgress }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    </div>
                                </div>
                            </div>

                            <div class="quality-stats d-flex justify-content-around mt-3">
                                <div class="text-center">
                                    <h6 class="mb-1">Target Checks</h6>
                                    <span class="fs-5 fw-bold">{{ $totalChecksNeeded }}</span>
                                </div>
                                <div class="text-center">
                                    <h6 class="mb-1">Completed</h6>
                                    <span class="fs-5 fw-bold">{{ $completedChecks }}</span>
                                </div>
                                <div class="text-center">
                                    <h6 class="mb-1">Interval</h6>
                                    <span class="fs-5 fw-bold">{{ $intervalCheck }} pcs</span>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Control and History Column -->
                        <div class="col-lg-8">
                        <!-- Production Control Card -->
                        <div class="card production-info-card">
                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center">
                                    <i class="bi bi-sliders me-2"></i>
                                    Kontrol Produksi
                                </h5>
                                
                                <!-- Control Buttons -->
                                <div class="control-buttons text-center mb-4">
                                    <div class="btn-group" role="group">
                                        @if ($activeProduction->status === 'running')
                                            <button class="btn btn-warning btn-lg px-4" 
                                                    wire:click="pauseProduction" 
                                                    wire:loading.attr="disabled">
                                                <i class="bi bi-pause-circle me-2"></i> Record Downtime
                                            </button>
                                            
                                            <button class="btn btn-danger btn-lg px-4" 
                                                    wire:click="$dispatch('show-problem-modal')" 
                                                    wire:loading.attr="disabled">
                                                <i class="bi bi-exclamation-triangle me-2"></i> Problem
                                            </button>
                                            <a href="{{ route('production.quality-check', ['productionId' => $activeProduction->id]) }}" 
                                               class="btn btn-info btn-lg px-4">
                                                <i class="bi bi-clipboard-check me-2"></i> Quality Check
                                            </a>
                                            <a href="{{ route('production.finish', ['productionId' => $activeProduction->id]) }}" 
                                               class="btn btn-success btn-lg px-4">
                                                <i class="bi bi-check-circle me-2"></i> Selesai
                                            </a>
                                        @endif

                                        @if ($activeProduction->status === 'paused')
                                            <button class="btn btn-primary btn-lg px-4" 
                                                    wire:click="resumeProduction" 
                                                    wire:loading.attr="disabled">
                                                <i class="bi bi-play-circle me-2"></i> Resume
                                            </button>
                                        @endif

                                        @if ($activeProduction->status === 'problem')
                                            @php
                                                $problem = $activeProduction->problems()->latest()->first();
                                            @endphp
                                            @if($problem && $problem->status === 'approved')
                                                <button class="btn btn-success btn-lg px-4" 
                                                        wire:click="resolveProblem" 
                                                        wire:loading.attr="disabled">
                                                    <i class="bi bi-check-circle me-2"></i> Resolve Problem
                                                </button>
                                            @endif
                                        @endif

                                        @if ($activeProduction->status === 'finished')
                                            <a href="{{ route('production.report', $activeProduction->id) }}" 
                                               class="btn btn-info btn-lg px-4">
                                                <i class="bi bi-file-pdf me-2"></i> Download Report
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @push('styles')
        <!-- Add these new styles to the existing styles section -->
        <style>
            .quality-history-card {
                margin-top: 1.5rem;
            }
            .table-custom {
                font-size: 0.95rem;
            }
            .table-custom th {
                background-color: #f8f9fa;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.85rem;
                letter-spacing: 0.5px;
            }
            .detail-btn {
                width: 32px;
                height: 32px;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
            }
            .modal-custom .modal-header {
                background-color: #f8f9fa;
                border-bottom: 2px solid #eef2f5;
            }
        </style>
        @endpush

     <!-- Add after the Production Control Card -->
                        <!-- Replace the entire Quality Check History section with this -->
                        <div class="card production-info-card quality-history-card">
                            <div class="card-body">
                                <h5 class="card-title d-flex align-items-center">
                                    <i class="bi bi-clock-history me-2"></i>
                                    Riwayat Quality Check
                                </h5>
                                
                                @if($qualityChecks && $qualityChecks->count() > 0)
                                    @foreach($qualityChecks as $index => $check)
                                        <div class="card mb-3 border shadow-sm">
                                            <div class="card-body py-3">
                                                <div class="row align-items-center">
                                                    <div class="col-md-1 text-center">
                                                        <span class="badge bg-secondary rounded-circle p-2">{{ $index + 1 }}</span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted d-block">Waktu Check</small>
                                                        <strong>{{ \Carbon\Carbon::parse($check->check_time)->format('H:i:s') }}</strong>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted d-block">Status</small>
                                                        <span class="badge rounded-pill bg-{{ $check->status === 'ok' ? 'success' : 'danger' }} px-3">
                                                            {{ strtoupper($check->status) }}
                                                        </span>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted d-block">Defect Count</small>
                                                        <strong>{{ $check->defect_count ?? '0' }}</strong>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted d-block">Inspector</small>
                                                        <strong>{{ $check->user->name }}</strong>
                                                    </div>
                                                    <div class="col-md-2 text-end">
                                                        <button type="button" class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#detailModal{{ $check->id }}">
                                                            <i class="bi bi-eye me-1"></i> Detail
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-clipboard-x fs-4 d-block mb-2"></i>
                                            Belum ada data pemeriksaan
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>

                <!-- Quality Check History section -->
                @if($qualityChecks && $qualityChecks->count() > 0)
                    @foreach($qualityChecks as $check)
                        <!-- Quality Check Modal -->
                        <div class="modal fade" id="detailModal{{ $check->id }}" tabindex="-1" wire:ignore.self 
                            data-bs-backdrop="static" role="dialog" aria-labelledby="detailModalLabel{{ $check->id }}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title d-flex align-items-center">
                                            <i class="bi bi-clipboard-data me-2"></i>
                                            Detail Quality Check
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Inspector & Status Info -->
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person-badge fs-4 me-2 text-primary"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Inspector</small>
                                                        <strong>{{ $check->user->name }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-clipboard-check fs-4 me-2 text-{{ $check->status === 'ok' ? 'success' : 'danger' }}"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Status</small>
                                                        <span class="badge bg-{{ $check->status === 'ok' ? 'success' : 'danger' }} rounded-pill px-3">
                                                            {{ strtoupper($check->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quality Check Details Table -->
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered table-striped align-middle">
                                                <thead class="table-light">
                                                    <tr class="text-center">
                                                        <th>Parameter</th>
                                                        <th>Standar</th>
                                                        <th>Terukur</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if($check->details && count($check->details) > 0)
                                                        @foreach($check->details as $detail)
                                                            <tr>
                                                                <td>{{ $detail->parameter }}</td>
                                                                <td class="text-center">{{ $detail->standard_value }}</td>
                                                                <td class="text-center">{{ $detail->measured_value }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge rounded-pill bg-{{ $detail->status === 'ok' ? 'success' : 'danger' }} px-3">
                                                                        {{ strtoupper($detail->status) }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td colspan="4" class="text-center">Tidak ada detail pemeriksaan</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>

                                        @if($check->notes)
                                            <div class="mt-3">
                                                <h6 class="d-flex align-items-center">
                                                    <i class="bi bi-pencil-square me-2"></i>
                                                    Catatan
                                                </h6>
                                                <p class="text-muted mb-0">{{ $check->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

              <!-- Downtime Modal -->
                <div class="modal fade" id="downtimeModal" tabindex="-1" wire:ignore.self>
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light">
                                <h5 class="modal-title d-flex align-items-center">
                                    <i class="bi bi-pause-circle me-2"></i>
                                    Record Downtime
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form wire:submit="saveDowntime">
                                    <div class="mb-3">
                                        <label class="form-label">Alasan Downtime</label>
                                        <textarea wire:model="reason" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Catatan (Opsional)</label>
                                        <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-save me-1"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Modal handlers for quality check details
                            const modals = document.querySelectorAll('.modal');
                            
                            Livewire.on('show-problem-modal', () => {
                                const problemModal = document.getElementById('problemModal');
                                if (problemModal) {
                                    const modal = new bootstrap.Modal(problemModal, {
                                        backdrop: 'static',
                                        keyboard: false
                                    });
                                    modal.show();
                                }
                            });
                
                            Livewire.on('openDowntimeModal', () => {
                                const downtimeModal = document.getElementById('downtimeModal');
                                if (downtimeModal) {
                                    const modal = new bootstrap.Modal(downtimeModal);
                                    modal.show();
                                }
                            });
                
                            Livewire.on('closeModal', () => {
                                modals.forEach(modal => {
                                    const bsModal = bootstrap.Modal.getInstance(modal);
                                    if (bsModal) {
                                        bsModal.hide();
                                    }
                                });
                                // Clean up backdrops
                                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                    backdrop.remove();
                                });
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                            });
                
                            // Success notifications
                            Livewire.on('success', (message) => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            });
                
                            // Error notifications
                            Livewire.on('error', (message) => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: message,
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            });
                        });
                    </script>
                @endpush
</div>
