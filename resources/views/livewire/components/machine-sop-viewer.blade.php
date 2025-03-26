@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/custom/pages/machine-sop-viewer.css') }}">
@endpush
<div>
    @if($sop)
        <div class="mt-4">
            <div class="sop-header d-flex justify-content-between align-items-center">
                <h5>Standard Operating Procedure (SOP)</h5>
                <div class="sop-badges">
                    <span class="badge bg-success">APPROVED</span>
                    <span class="badge bg-primary">ACTIVE</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="sop-info-table">
                        <table class="table table-sm">
                            <tr>
                                <td width="30%">NO. SOP</td>
                                <td>: {{ $sop->no_sop }}</td>
                            </tr>
                            <tr>
                                <td>NAMA SOP</td>
                                <td>: {{ $sop->nama }}</td>
                            </tr>
                            <tr>
                                <td>KATEGORI</td>
                                <td>: {{ $sop->kategori }}</td>
                            </tr>
                            <tr>
                                <td>VERSI</td>
                                <td>: {{ $sop->versi }}</td>
                            </tr>
                            <tr>
                                <td>MESIN</td>
                                <td>: {{ $sop->machine->name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="sop-info-table">
                        <table class="table table-sm">
                            <tr>
                                <td width="40%">DIBUAT OLEH</td>
                                <td>: {{ $sop->creator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>DIBUAT PADA</td>
                                <td>: {{ $sop->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td>DISETUJUI OLEH</td>
                                <td>: {{ $sop->approver->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td>DISETUJUI PADA</td>
                                <td>: {{ $sop->approved_at?->format('d/m/Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="steps-table">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="5%">NO.</th>
                            <th width="20%">STEP NAME</th>
                            <th>DESCRIPTION</th>
                            <th width="15%">IMAGE</th>
                            <th width="15%">CHECKPOINT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sop->steps()->orderBy('urutan')->get() as $step)
                            <tr>
                                <td class="text-center">{{ $step->urutan }}</td>
                                <td>{{ $step->judul }}</td>
                                <td>{{ $step->deskripsi }}</td>
                                <td class="text-center">
                                    @if($step->cloudinary_url)
                                        <div class="step-image">
                                            <img src="{{ $step->cloudinary_url }}" 
                                                 alt="Step Image" 
                                                 class="img-fluid"
                                                 style="max-height: 80px; cursor: pointer;"
                                                 onclick="window.open(this.src, '_blank')">
                                        </div>
                                    @elseif($step->cloudinary_id)
                                        <div class="step-image">
                                            <img src="https://res.cloudinary.com/dncabigef/image/upload/v1/{{ $step->cloudinary_id }}" 
                                                 alt="Step Image" 
                                                 class="img-fluid"
                                                 style="max-height: 80px; cursor: pointer;"
                                                 onclick="window.open(this.src, '_blank')">
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($step->is_checkpoint)
                                        <div class="checkpoint-info">
                                            <strong>Nilai Standar:</strong> {{ $step->nilai_standar }}<br>
                                            <strong>Toleransi:</strong><br>
                                            {{ $step->toleransi_min }} - {{ $step->toleransi_max }}
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>