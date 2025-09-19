<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ANIME LIST</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">Daftar Anime</h1>
        <a href="{{ route('anime.create') }}" class="btn btn-primary mb-3">Tambah Anime</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Genre</th>
                    <th>Episode</th>
                    <th>Sinopsis</th>
                    <th>Gambar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anime as $item)
                    <tr>
                        <td>{{ $item->judul }}</td>
                        <td>{{ $item->genre }}</td>
                        <td>{{ number_format($item->episode, 0, ',', '.') }}</td>
                        <td>{!! Str::words($item->sinopsis, 20, '...') !!}</td>
                        <td><img src="{{ asset('storage/anime/' . $item->gambar) }}"
                                alt="{{ $item->judul }}" width="100"></td>
                        <td>
                            <form onsubmit="return confirm('Apakah Anda yakin ingin menghapus anime ini?');"
                                action="{{ route('anime.destroy', $item->id) }}" method="POST">
                                <a href="{{ route('anime.show', $item->id) }}" class="btn btn-success btn-sm" target="_blank">Detail</a>
                                <a href="{{ route('anime.edit', $item->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <div class="alert alert-danger">
                        Data Anime belum Tersedia.
                    </div>
                @endforelse
            </tbody>
        </table>
        {{ $anime->links() }}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @elseif (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif
    </script>

</body>

</html>