<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anime: {{ $anime->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .form-label {
            font-weight: bold;
        }
        .form-control, .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .ck-editor__editable_inline {
            min-height: 250px;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <h1 class="mb-4 text-center">Edit Anime: {{ $anime->judul }}</h1>
        
        <form action="{{ route('anime.update', $anime->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') 

            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" value="{{ old('judul', $anime->judul) }}" required>
            </div>
            
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select name="genres[]" id="genre" class="form-control" multiple required>
                    @foreach($genres as $genre)
                        <option value="{{ $genre }}" 
                            @if(in_array($genre, $selectedGenres)) selected @endif>
                            {{ $genre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="episode" class="form-label">Episode</label>
                <input type="number" name="episode" id="episode" class="form-control" value="{{ old('episode', $anime->episode) }}" required>
            </div>

            <div class="mb-3">
                <label for="sinopsis" class="form-label">Sinopsis</label>
                <textarea name="sinopsis" id="sinopsis" rows="10" class="form-control">{{ old('sinopsis', $anime->sinopsis) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="gambar" class="form-label">Gambar (Kosongkan jika tidak ingin diubah)</label>
                <input type="file" name="gambar" id="gambar" class="form-control-file">
                @if($anime->gambar)
                    <small class="text-muted">Gambar saat ini: **{{ $anime->gambar }}**</small>
                @endif
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-warning">Update Anime</button>
                <a href="{{ route('anime.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#genre').select2({
                placeholder: "Pilih genre",
                allowClear: true
            });
            ClassicEditor.create( document.querySelector( '#sinopsis' ) ).catch( error => { console.error( error ); } );
        });
    </script>
</body>
</html>