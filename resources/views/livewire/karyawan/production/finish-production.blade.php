<div>
    <div class="pagetitle">
        <h1>Selesaikan Produksi</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('karyawan.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('production.status') }}">Status Produksi</a></li>
                <li class="breadcrumb-item active">Selesaikan Produksi</li>
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

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Form Penyelesaian Produksi</h5>
                        
                        <form wire:submit.prevent="finish">
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Total Produksi</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" wire:model.live="totalProduction" min="1" required>
                                    @error('totalProduction') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Total Reject</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" wire:model="totalReject">
                                    @error('totalReject') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Catatan</label>
                                <div class="col-sm-9">
                                    <textarea class="form-control" wire:model="notes" rows="3"></textarea>
                                    @error('notes') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-sm-9 offset-sm-3">
                                    <div class="text-end mt-3">
                                        <button type="submit" class="btn btn-primary" wire:click="$dispatch('openFinishModal')">
                                            Selesaikan Produksi
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Modal Konfirmasi Download -->
    <div class="modal fade" id="finishModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Produksi Selesai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Produksi telah berhasil diselesaikan.</p>
                    <p>Apakah Anda ingin mengunduh laporan produksi?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="{{ route('production.report', ['productionId' => $production->id]) }}" 
                       class="btn btn-primary" target="_blank">
                        Download Laporan
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Initialize modal when component loads
        let finishModal;
        
        document.addEventListener('DOMContentLoaded', () => {
            finishModal = new bootstrap.Modal(document.getElementById('finishModal'));
            console.log('Modal initialized');
        });

        // Listen for the finish event
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('finish-success', () => {
                console.log('Showing modal');
                if (finishModal) {
                    finishModal.show();
                } else {
                    console.error('Modal not initialized');
                }
            });
        });
    </script>
</div>