<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Category;

class CategoryCrud extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name, $description, $category_id;
    public $isEditMode = false;

    // Validasi
    protected $rules = [
        'name' => 'required|min:3',
        'description' => 'nullable|string',
    ];

    public function render()
    {
        return view('livewire.admin.category-crud', [
            'categories' => Category::latest()->paginate(5),
        ]);
    }

    // Reset Form
    private function resetInputFields(){
        $this->name = '';
        $this->description = '';
        $this->category_id = '';
        $this->isEditMode = false;
    }

    // Fungsi Simpan Data
    public function store()
    {
        $this->validate();

        Category::create([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('message', 'Kategori Berhasil Ditambahkan.');
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }

    // Fungsi Ambil Data untuk Edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->category_id = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->isEditMode = true;
        
        $this->dispatch('open-modal');
    }

    // Fungsi Update Data
    public function update()
    {
        $this->validate();

        if ($this->category_id) {
            $category = Category::find($this->category_id);
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            
            session()->flash('message', 'Kategori Berhasil Diupdate.');
            $this->resetInputFields();
            $this->dispatch('close-modal');
        }
    }

    // Fungsi Hapus Data
    public function delete($id)
    {
        Category::find($id)->delete();
        session()->flash('message', 'Kategori Berhasil Dihapus.');
    }
    
    // Fungsi Cancel
    public function cancel()
    {
        $this->resetInputFields();
        $this->dispatch('close-modal');
    }
}