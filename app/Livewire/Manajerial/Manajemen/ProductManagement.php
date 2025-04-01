<?php

namespace App\Livewire\Manajerial\Manajemen;

use Livewire\Component;
use App\Models\Product;

class ProductManagement extends Component
{
    public $name;
    public $code;
    public $product_code; // Tambah ini
    public $description;
    public $target_per_hour;
    public $target_per_shift;
    public $target_per_day;
    public $isEditing = false;
    public $editId;
    public $unit;
    public $cycle_time;  // Add this property

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:products,code',
        'product_code' => 'required|string|max:50|unique:products,product_code', // Tambah ini
        'description' => 'nullable|string',
        'unit' => 'required|string|max:20',
        'target_per_hour' => 'nullable|integer|min:0',
        'target_per_shift' => 'nullable|integer|min:0',
        'target_per_day' => 'nullable|integer|min:0',
        'cycle_time' => 'required|numeric|min:0'  // Add this rule
    ];

    public function edit($id)
    {
        $this->isEditing = true;
        $this->editId = $id;
        $product = Product::find($id);
        
        $this->name = $product->name;
        $this->code = $product->code;
        $this->product_code = $product->product_code; // Add this line
        $this->description = $product->description;
        $this->target_per_hour = $product->target_per_hour;
        $this->target_per_shift = $product->target_per_shift;
        $this->target_per_day = $product->target_per_day;
        $this->unit = $product->unit;
        $this->cycle_time = $product->cycle_time;
    }

    public function store()
    {
        $this->validate();

        Product::create([
            'name' => $this->name,
            'code' => $this->code,
            'product_code' => $this->product_code, // Tambah ini
            'description' => $this->description,
            'unit' => $this->unit,
            'target_per_hour' => $this->target_per_hour,
            'target_per_shift' => $this->target_per_shift,
            'target_per_day' => $this->target_per_day,
            'cycle_time' => $this->cycle_time  // Add this
        ]);

        $this->reset();
        session()->flash('success', 'Produk berhasil ditambahkan');
    }

    public function update()
    {
        $this->rules['code'] = 'required|string|max:50|unique:products,code,' . $this->editId;
        $this->rules['product_code'] = 'required|string|max:50|unique:products,product_code,' . $this->editId;
        $this->validate();

        Product::find($this->editId)->update([
            'name' => $this->name,
            'code' => $this->code,
            'product_code' => $this->product_code, // Add this line
            'description' => $this->description,
            'unit' => $this->unit,
            'target_per_hour' => $this->target_per_hour,
            'target_per_shift' => $this->target_per_shift,
            'target_per_day' => $this->target_per_day,
            'cycle_time' => $this->cycle_time
        ]);

        $this->cancelEdit();
        session()->flash('success', 'Produk berhasil diperbarui');
    }

    public function delete($id)
    {
        $product = Product::find($id);
        
        if ($product->sops()->count() > 0) {
            session()->flash('error', 'Produk tidak dapat dihapus karena masih terkait dengan SOP');
            return;
        }
        
        $product->delete();
        session()->flash('success', 'Produk berhasil dihapus');
    }

    public function cancelEdit()
    {
        $this->reset();
        $this->isEditing = false;
    }

    public function render()
    {
        return view('livewire.manajerial.manajemen.product-management', [
            'products' => Product::orderBy('created_at', 'desc')->get()
        ]);
    }
}