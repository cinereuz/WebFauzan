<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card card-custom p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-white">Daftar Kategori</h4>
            <button wire:click="cancel()" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-hover rounded">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $cat)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $cat->name }}</td>
                        <td>{{ $cat->description }}</td>
                        <td>
                            <button wire:click="edit({{ $cat->id }})" class="btn btn-primary btn-sm">Edit</button>
                            <button wire:click="delete({{ $cat->id }})" onclick="return confirm('Yakin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>

    {{-- MODAL FORM (Bootstrap) --}}
    <div wire:ignore.self class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: #2a2a4a; color: white;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title" id="exampleModalLabel">
                        {{ $isEditMode ? 'Edit Kategori' : 'Tambah Kategori' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" wire:click="cancel()"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label>Nama Kategori</label>
                            <input type="text" class="form-control" wire:model="name" style="background-color: #1c1c2e; color: white; border-color: #404060;">
                            @error('name') <span class="text-danger error small">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" wire:model="description" style="background-color: #1c1c2e; color: white; border-color: #404060;"></textarea>
                            @error('description') <span class="text-danger error small">{{ $message }}</span> @enderror
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="cancel()">Tutup</button>
                    @if($isEditMode)
                        <button type="button" wire:click.prevent="update()" class="btn btn-primary">Update</button>
                    @else
                        <button type="button" wire:click.prevent="store()" class="btn btn-success">Simpan</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>