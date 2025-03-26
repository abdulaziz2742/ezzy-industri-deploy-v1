<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 5%">No.</th>
                <th style="width: 30%">Step Name</th>
                <th style="width: 45%">Description</th>
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
                    <td class="text-center">
                        @if($step->gambar_path)
                            <img src="{{ $step->gambar_path }}" 
                                 alt="Step Image" 
                                 class="img-thumbnail" 
                                 style="max-height: 50px;">
                        @else
                            -
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
                    <td colspan="5" class="text-center">Belum ada langkah yang ditambahkan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>