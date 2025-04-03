@push('styles')
<link href="{{ asset('assets/css/custom/pages/dashboard.css') }}" rel="stylesheet">
@endpush

<div class="dashboard">
    <!-- Dashboard Header with Period Filter -->
    <div class="dashboard-header">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="fw-bold">Dashboard Karyawan</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active">Overview</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="btn-group" role="group">
                    <button wire:click="setPeriod('today')" class="btn btn-{{ $selectedPeriod === 'today' ? 'primary' : 'outline-primary' }}">
                        Hari Ini
                    </button>
                    <button wire:click="setPeriod('week')" class="btn btn-{{ $selectedPeriod === 'week' ? 'primary' : 'outline-primary' }}">
                        Minggu Ini
                    </button>
                    <button wire:click="setPeriod('month')" class="btn btn-{{ $selectedPeriod === 'month' ? 'primary' : 'outline-primary' }}">
                        Bulan Ini
                    </button>
                </div>
                <div class="date-display">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Production Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Produksi</h6>
                    <h2 class="card-title mb-2">{{ $todayProduction }}</h2>
                    <p class="card-text text-muted">pcs</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Total Downtime</h6>
                    <h2 class="card-title mb-2">{{ $totalDowntime }}</h2>
                    <p class="card-text text-muted">menit</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Defect</h6>
                    <h2 class="card-title mb-2">{{ $todayDefects }}</h2>
                    <p class="card-text text-muted">pcs</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Quality Rate</h6>
                    <h2 class="card-title mb-2">{{ $oeeData?->quality_rate ?? 0 }}%</h2>
                    <p class="card-text text-muted">tingkat kualitas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- OEE & Target Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">OEE Performance</h5>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span>Availability Rate</span>
                            <span class="fw-bold">{{ $oeeData?->availability_rate ?? 0 }}%</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-primary" style="width: {{ $oeeData?->availability_rate ?? 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Performance Rate</span>
                            <span class="fw-bold">{{ $oeeData?->performance_rate ?? 0 }}%</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" style="width: {{ $oeeData?->performance_rate ?? 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Quality Rate</span>
                            <span class="fw-bold">{{ $oeeData?->quality_rate ?? 0 }}%</span>
                        </div>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" style="width: {{ $oeeData?->quality_rate ?? 0 }}%"></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Overall OEE</span>
                            <span class="fw-bold">{{ $oeeData?->oee_score ?? 0 }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Target vs Realisasi</h5>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="mb-0">{{ $productionRealization }}</h3>
                                <small class="text-muted">dari target {{ $productionTarget }}</small>
                            </div>
                            <div class="progress" style="width: 70%; height: 20px;">
                                @php
                                    $percentage = $productionTarget > 0 ? ($productionRealization / $productionTarget) * 100 : 0;
                                @endphp
                                <div class="progress-bar {{ $percentage >= 100 ? 'bg-success' : 'bg-warning' }}" 
                                     style="width: {{ min($percentage, 100) }}%">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                        <!-- Tambahan informasi -->
                        <div class="text-end">
                            @if($percentage == 100)
                                <span class="badge bg-success">Target Tercapai</span>
                            @elseif($percentage > 100)
                                <span class="badge bg-info">Melebihi Target</span>
                            @else
                                <span class="badge bg-warning">Belum Mencapai Target</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Trend Performa</h5>
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Recent Activity Section -->
    <div class="row g-4">
        <!-- ... -->
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('livewire:initialized', () => {
    const performanceData = @json($performanceData);
    
    const ctx = document.getElementById('performanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: performanceData.map(item => item.date),
            datasets: [
                {
                    label: 'OEE Score',
                    data: performanceData.map(item => item.oee_score),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                },
                {
                    label: 'Availability',
                    data: performanceData.map(item => item.availability_rate),
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                },
                {
                    label: 'Performance',
                    data: performanceData.map(item => item.performance_rate),
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1
                },
                {
                    label: 'Quality',
                    data: performanceData.map(item => item.quality_rate),
                    borderColor: 'rgb(255, 205, 86)',
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
@endpush