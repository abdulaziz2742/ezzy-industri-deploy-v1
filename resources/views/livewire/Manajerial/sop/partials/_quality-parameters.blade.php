<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 5%">No.</th>
                <th style="width: 15%">Parameter</th>
                <th style="width: 15%">Description</th>
                <th style="width: 10%">Standard</th>
                <th style="width: 10%">Tolerance</th>
                <th style="width: 10%">Unit</th>
                <th style="width: 15%">Check Interval</th>
                <th style="width: 10%">Image</th>
                <th style="width: 10%">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sop->steps as $step)
                <tr>
                    <td class="text-center">{{ $step->urutan }}</td>
                    <td>{{ $step->judul }}</td>
                    <td>{{ $step->deskripsi }}</td>
                    <td class="text-center">{{ $step->nilai_standar }}</td>
                    <td class="text-center">{{ $step->toleransi_min }} - {{ $step->toleransi_max }}</td>
                    <td class="text-center">
                        {{ $step->measurement_unit }}
                        <div class="small text-muted">{{ $step->measurement_type }}</div>
                    </td>
                    <td class="text-center">
                        Every {{ $step->interval_value }} {{ $step->interval_unit }}
                    </td>
                    <td class="text-center">
                        @if($step->cloudinary_url)
                            <img src="{{ $step->cloudinary_url }}" 
                                 alt="Quality Parameter Image" 
                                 class="img-thumbnail" 
                                 style="max-height: 50px; cursor: pointer;"
                                 onclick="window.open(this.src, '_blank')"
                            >
                        @elseif($step->cloudinary_id)
                            <img src="https://res.cloudinary.com/dncabigef/image/upload/v1/{{ $step->cloudinary_id }}" 
                                 alt="Quality Parameter Image" 
                                 class="img-thumbnail" 
                                 style="max-height: 50px; cursor: pointer;"
                                 onclick="window.open(this.src, '_blank')"
                            >
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-warning" wire:click="edit({{ $step->id }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button wire:click="confirmDelete({{ $step->id }})" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Belum ada parameter yang ditambahkan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>