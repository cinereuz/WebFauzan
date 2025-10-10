<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Anime</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root { --primary-bg: #1c1c2e; --secondary-bg: #2a2a4a; --text-color: #e0e0f0; --accent-color-1: #ff6b6b; --accent-color-2: #4ecdc4; }
        body { background-color: var(--primary-bg); color: var(--text-color); font-family: 'Poppins', sans-serif; min-height: 100vh; }
        .container { background-color: var(--secondary-bg); border-radius: 15px; padding: 40px; box-shadow: 0 8px 25px rgba(0,0,0,0.4); margin-top: 50px; max-width: 800px; border-bottom: 5px solid var(--accent-color-2); }
        h1 { color: var(--accent-color-1); font-weight: 700; font-size: 2rem; }
        .form-label { font-weight: 600; color: var(--text-color); font-size: 0.9rem; }
        
        .form-control:not([type="file"]), 
        .select2-container--default .select2-selection--multiple,
        .select2-container--default .select2-dropdown {
            background-color: #38385e; 
            border: 1px solid #4a4a70; 
            color: var(--text-color) !important; 
        }
        .form-control:focus, .select2-container--default.select2-container--focus .select2-selection--multiple { 
            border-color: var(--accent-color-2); 
            box-shadow: 0 0 0 0.25rem rgba(78, 205, 196, 0.3); 
            background-color: #38385e; 
        }

        .select2-container--default .select2-results__option {
            color: var(--text-color) !important;
            background-color: black !important;
        }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #4a4a70 !important;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice { background-color: var(--accent-color-2); border: none; color: var(--secondary-bg); }
        
        .ck.ck-toolbar {
            background-color: #2a2a4a !important;
            border-bottom: 1px solid #4a4a70;
            color: var(--text-color) !important;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        .ck.ck-content {
            background-color: #38385e !important;
            color: var(--text-color) !important;
            min-height: 250px;
            border-bottom-left-radius: 5px;
            border-bottom-right-radius: 5px;
            border: 1px solid #4a4a70;
            border-top: none;
        }
        .ck.ck-content, .ck.ck-editor__editable {
            color: var(--text-color) !important;
        }
        .ck.ck-button {
            color: var(--text-color) !important;
        }
        .ck.ck-button:hover { 
            color: black !important;
        }
        
        .form-control:not([type="file"]) {
             color: var(--text-color) !important;
        }
    </style>
</head>
<body>
    <div class="container my-4">
        <h1 class="mb-4 text-center">Tambah Anime Baru</h1>
        <form action="{{ route('anime.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="judul" class="form-label">Judul</label>
                    <input type="text" name="judul" id="judul" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="episode" class="form-label">Episode</label>
                    <input type="number" name="episode" id="episode" class="form-control" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="genre" class="form-label">Genre</label>
                <select name="genres[]" id="genre" class="form-control" multiple required>
                    @foreach($genres as $genre)
                        <option value="{{ $genre }}">{{ $genre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-3">
                <label for="sinopsis" class="form-label">Sinopsis</label>
                <textarea name="sinopsis" id="sinopsis" rows="10" class="form-control"></textarea>
            </div>
            <div class="mb-4">
                <label for="gambar" class="form-label">Gambar</label>
                <input type="file" name="gambar" id="gambar" class="form-control" required>
            </div>
            <div class="d-flex justify-content-end pt-3 border-top border-secondary">
                <a href="{{ route('anime.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-primary" style="background-color: var(--accent-color-2); border-color: var(--accent-color-2);">Simpan Anime</button>
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/41.3.1/classic/ckeditor.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#genre').select2({ placeholder: "Pilih genre", allowClear: true });
            ClassicEditor.create( document.querySelector( '#sinopsis' ) ).then( editor => {
            }).catch( error => { console.error( error ); } );
        });
    </script>
</body>
</html>