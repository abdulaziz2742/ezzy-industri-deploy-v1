<?php

namespace App\Livewire\Manajerial\Manajemen;

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class UserManagement extends Component
{
    use WithPagination;

    // Move existing properties below these two lines
    public $name;
    public $email;
    public $employee_id; // Tambah ini
    public $role = 'karyawan';
    public $password;
    public $department_id;
    public $userId;
    public $isEditing = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'employee_id' => 'required|string|max:50|unique:users,employee_id', // Tambah ini
        'role' => 'required|in:manajerial,karyawan',
        'password' => 'required|min:6',
        'department_id' => 'nullable|exists:departments,id'
    ];


    // Fix the render method for better search
    public function render()
    {
        $users = User::query()
            ->with('department')
            ->where(function($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10);

        return view('livewire.manajerial.manajemen.user-management', [
            'users' => $users,
            'departments' => Department::all()
        ]);
    }


    // Add updatedSearch method for live search
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'employee_id' => $this->employee_id, // Tambah ini
            'role' => $this->role,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id
        ]);

        $this->reset(['name', 'email', 'role', 'password', 'department_id']);
        session()->flash('message', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $this->isEditing = true;
        $this->userId = $id;
        $user = User::find($id);
        $this->name = $user->name;
        $this->email = $user->email;
        $this->employee_id = $user->employee_id; // Add this
        $this->role = $user->role;
        $this->department_id = $user->department_id;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$this->userId,
            'employee_id' => 'required|string|max:50|unique:users,employee_id,'.$this->userId,
            'role' => 'required|in:manajerial,karyawan',
            'department_id' => 'nullable|exists:departments,id'
        ]);

        $user = User::find($this->userId);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'employee_id' => $this->employee_id, // Add this
            'role' => $this->role,
            'department_id' => $this->department_id
        ]);

        $this->reset(['name', 'email', 'employee_id', 'role', 'password', 'department_id', 'isEditing', 'userId']); // Add employee_id
        session()->flash('message', 'User berhasil diupdate.');
    }

    public function resetForm()
    {
        $this->reset(['name', 'email', 'employee_id', 'role', 'password', 'department_id', 'isEditing', 'userId']); // Add employee_id
    }

    public function delete($id)
    {
        $user = User::find($id);
        
        // Prevent deleting your own account
        if ($user->id === auth::id()) {
            session()->flash('error', 'Tidak dapat menghapus akun yang sedang digunakan.');
            return;
        }

        $user->delete();
        session()->flash('message', 'User berhasil dihapus.');
    }
}