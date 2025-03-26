<div wire:poll.{{ $refreshInterval }}ms>
    <div class="pagetitle">
        <h1>Detail OEE {{ $machine->name }}</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manajerial.oee.dashboard') }}">OEE Dashboard</a></li>
                <li class="breadcrumb-item active">Detail OEE</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Periode</label>
                    <select class="form-select" wire:model.live="selectedPeriod">
                        <option value="daily">Harian</option>
                        <option value="weekly">Mingguan</option>
                        <option value="monthly">Bulanan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <a href="{{ route('manajerial.oee.detail.pdf', ['machineId' => $machine->id]) }}" 
                       class="btn btn-danger mt-4" 
                       target="_blank">
                        <i class="bi bi-file-pdf"></i> Download PDF
                    </a>
                </div>
                <div class="col-md-4 text-end">
                    <div class="alert alert-info p-2 mt-4">
                        <small><i class="bi bi-clock"></i> Terakhir diperbarui: {{ $lastUpdated }}</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Availability Rate</h5>
                            <div class="metric-value {{ $averageAvailability < 60 ? 'text-danger' : ($averageAvailability < 85 ? 'text-warning' : 'text-success') }}">
                                {{ number_format($averageAvailability, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Performance Rate</h5>
                            <div class="metric-value {{ $averagePerformance < 60 ? 'text-danger' : ($averagePerformance < 85 ? 'text-warning' : 'text-success') }}">
                                {{ number_format($averagePerformance, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Quality Rate</h5>
                            <div class="metric-value {{ $averageQuality < 60 ? 'text-danger' : ($averageQuality < 85 ? 'text-warning' : 'text-success') }}">
                                {{ number_format($averageQuality, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">OEE Score</h5>
                            <div class="metric-value {{ $oeeScore < 60 ? 'text-danger' : ($oeeScore < 85 ? 'text-warning' : 'text-success') }}">
                                {{ number_format($oeeScore, 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">OEE Trend</h5>
                                <div id="oeeChart"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        window.initialChartData = @js($chartData);
    </script>
    <script src="{{ asset('assets/js/oee-chart.js') }}"></script>
    @endpush
</div>
