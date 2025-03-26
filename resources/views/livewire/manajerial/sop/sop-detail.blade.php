<div>
    <div class="pagetitle">
        <div class="d-flex justify-content-between align-items-center">
            <h1>Detail SOP: {{ $sop->nama }}</h1>
            <div>
                @if($sop->approval_status === 'approved')
                    <button type="button" class="btn btn-{{ $sop->is_active ? 'danger' : 'success' }} me-2" 
                            wire:click="toggleActive"
                            wire:confirm="{{ $sop->is_active ? 'Nonaktifkan SOP ini?' : 'Aktifkan kembali SOP ini?' }}">
                        <i class="bi bi-{{ $sop->is_active ? 'toggle-off' : 'toggle-on' }}"></i>
                        {{ $sop->is_active ? 'Nonaktifkan SOP' : 'Aktifkan SOP' }}
                    </button>
                    <a href="{{ route('manajerial.sop.pdf', $sop->id) }}" class="btn btn-info me-2" target="_blank">
                        <i class="bi bi-file-pdf"></i> Cetak PDF
                    </a>
                @endif
                <a href="{{ route('manajerial.sop') }}" class="btn btn-secondary" wire:navigate>
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('manajerial.sop') }}">Master SOP</a></li>
                <li class="breadcrumb-item active">Detail SOP</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <!-- Info Panel -->
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">NO. SOP</th>
                                        <td>{{ $sop->no_sop ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>NAMA SOP</th>
                                        <td>{{ $sop->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>KATEGORI</th>
                                        <td>{{ ucfirst($sop->kategori) }}</td>
                                    </tr>
                                    <tr>
                                        <th>VERSI</th>
                                        <td>{{ $sop->versi }}</td>
                                    </tr>
                                    @if($sop->kategori === 'produksi' || $sop->kategori === 'safety')
                                    <tr>
                                        <th>MESIN</th>
                                        <td>{{ $sop->machine->name ?? '-' }}</td>
                                    </tr>
                                    @endif
                                    @if($sop->kategori === 'quality')
                                    <tr>
                                        <th>PRODUK</th>
                                        <td>{{ $sop->product->name ?? '-' }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm">
                                    <tr>
                                        <th width="40%">STATUS</th>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $sop->approval_status === 'approved' ? 'success' : 
                                                ($sop->approval_status === 'pending' ? 'warning' : 
                                                ($sop->approval_status === 'rejected' ? 'danger' : 'secondary')) 
                                            }}">
                                                {{ ucfirst($sop->approval_status) }}
                                            </span>
                                            @if($sop->approval_status === 'approved')
                                                <span class="badge bg-{{ $sop->is_active ? 'success' : 'danger' }} ms-2">
                                                    {{ $sop->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>DIBUAT OLEH</th>
                                        <td>{{ $sop->creator->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>DIBUAT PADA</th>
                                        <td>{{ $sop->created_date ? $sop->created_date->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>DISETUJUI OLEH</th>
                                        <td>{{ $sop->approver->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>DISETUJUI PADA</th>
                                        <td>{{ $sop->approved_at ? $sop->approved_at->format('d/m/Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        @if($sop->approval_status === 'draft')
                            <div class="text-end mt-3">
                                <button type="button" class="btn btn-primary" wire:click="submitForApproval">
                                    <i class="bi bi-send"></i> Submit untuk Persetujuan
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
                        <!-- Parameter/Steps Table -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">
                                            @if($sop->kategori === 'quality')
                                                Quality Check Parameters
                                            @elseif($sop->kategori === 'produksi')
                                                Production Steps
                                            @else
                                                Safety Check Steps
                                            @endif
                                        </h5>
                                        <button type="button" class="btn btn-primary" wire:click="openModal">
                                            <i class="bi bi-plus"></i> Add {{ $sop->kategori === 'quality' ? 'Parameter' : 'Step' }}
                                        </button>
                                    </div>
                                    
                                    @if($sop->kategori === 'quality')
                                        @include('livewire.manajerial.sop.partials._quality-parameters')
                                    @else
                                        @include('livewire.manajerial.sop.partials._production-steps')
                                    @endif
                                </div>
                            </div>
                        </div>
        </div>
    </section>
        <!-- Modal Form -->
        @if($showModal)
    </section>
        <!-- Modal Form -->
        @if($showModal)
        <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEditing ? 'Edit' : 'Add New' }} 
                            {{ $sop->kategori === 'quality' ? 'Parameter' : 'Step' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}">
                        <div class="modal-body">
                            @if($sop->kategori === 'quality')
                                @include('livewire.manajerial.sop.partials._quality-form')
                            @else
                                @include('livewire.manajerial.sop.partials._production-form')
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-backdrop fade show"></div>
        @endif
    </div>
    @endif
    
</div>