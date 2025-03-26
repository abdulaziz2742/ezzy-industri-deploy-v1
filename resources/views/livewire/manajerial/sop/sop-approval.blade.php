<div>
    <div class="pagetitle">
        <h1>SOP Approval</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('manajerial.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">SOP Approval</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Pending SOP Approvals</h5>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No. SOP</th>
                                <th>Nama SOP</th>
                                <th>Kategori</th>
                                <th>Diajukan Oleh</th>
                                <th>Diajukan Pada</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sops as $sop)
                            <tr>
                                <td>{{ $sop->no_sop }}</td>
                                <td>{{ $sop->nama }}</td>
                                <td>{{ ucfirst($sop->kategori) }}</td>
                                <td>{{ $sop->creator->name }}</td>
                                <td>{{ $sop->submitted_at ? $sop->submitted_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('manajerial.sop.detail', $sop->id) }}" 
                                           class="btn btn-sm btn-info" wire:navigate>
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button class="btn btn-sm btn-success" 
                                                wire:click="approve({{ $sop->id }})"
                                                wire:confirm="Are you sure you want to approve this SOP?">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" 
                                                wire:click="showRejectModal({{ $sop->id }})">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada SOP yang menunggu persetujuan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Rejection Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0, 0, 0, 0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject SOP: {{ $sop->nama }}</h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <form wire:submit.prevent="reject">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea class="form-control" wire:model="rejection_reason" 
                                      rows="3" placeholder="Berikan alasan penolakan..."></textarea>
                            @error('rejection_reason') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" 
                                wire:click="$set('showModal', false)">Cancel</button>
                        <button type="submit" class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif
</div>