<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guest;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDrawer = false;
    public bool $isEditMode = false;

    public ?int $guestModelId = null;
    public string $guest_id = '', $name = '', $email = '', $phone = '',
                  $nationality = '', $passport_number = '', $address = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $guest = Guest::findOrFail($id);
        $this->guestModelId     = $guest->id;
        $this->guest_id         = $guest->guest_id;
        $this->name             = $guest->name;
        $this->email            = $guest->email ?? '';
        $this->phone            = $guest->phone ?? '';
        $this->nationality      = $guest->nationality ?? '';
        $this->passport_number  = $guest->passport_number ?? '';
        $this->address          = $guest->address ?? '';
        $this->isEditMode       = true;
        $this->showDrawer       = true;
    }

    public function store(): void
    {
        $this->validate([
            'guest_id'       => 'required|unique:guests,guest_id,' . $this->guestModelId,
            'name'           => 'required|string|max:255',
            'email'          => 'nullable|email|unique:guests,email,' . $this->guestModelId,
            'phone'          => 'nullable|string|max:20',
            'nationality'    => 'nullable|string|max:100',
            'passport_number'=> 'nullable|string|max:100',
            'address'        => 'nullable|string',
        ]);

        Guest::updateOrCreate(['id' => $this->guestModelId], [
            'guest_id'        => $this->guest_id,
            'name'            => $this->name,
            'email'           => $this->email ?: null,
            'phone'           => $this->phone ?: null,
            'nationality'     => $this->nationality ?: null,
            'passport_number' => $this->passport_number ?: null,
            'address'         => $this->address ?: null,
        ]);

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Guest saved successfully!', type: 'success');
    }

    public function delete(int $id): void
    {
        Guest::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Guest deleted.', type: 'success');
    }

    private function resetFields(): void
    {
        $this->guestModelId    = null;
        $this->guest_id        = 'G-' . str_pad(rand(1000, 99999), 5, '0', STR_PAD_LEFT);
        $this->name            = '';
        $this->email           = '';
        $this->phone           = '';
        $this->nationality     = '';
        $this->passport_number = '';
        $this->address         = '';
        $this->isEditMode      = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $guests = Guest::where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('guest_id', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return $this->view(['guests' => $guests]);
    }
};
