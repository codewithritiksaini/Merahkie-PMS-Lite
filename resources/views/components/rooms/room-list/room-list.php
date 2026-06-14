<?php

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use App\Models\Room;
use App\Models\RoomType;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDrawer = false;
    public bool $isEditMode = false;

    public ?int $roomId = null;
    #[Validate('required|string|max:20')] public string $room_number = '';
    #[Validate('required|exists:room_types,id')] public string $room_type_id = '';
    #[Validate('required|numeric|min:0')] public string $price = '';
    #[Validate('required|in:Available,Occupied,Reserved,Maintenance')] public string $status = 'Available';

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $room = Room::findOrFail($id);
        $this->roomId      = $room->id;
        $this->room_number = $room->room_number;
        $this->room_type_id = (string)$room->room_type_id;
        $this->price       = (string)$room->price;
        $this->status      = $room->status;
        $this->isEditMode  = true;
        $this->showDrawer  = true;
    }

    public function store(): void
    {
        $this->validate([
            'room_number'  => 'required|unique:rooms,room_number,' . $this->roomId,
            'room_type_id' => 'required|exists:room_types,id',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        Room::updateOrCreate(['id' => $this->roomId], [
            'room_number'  => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'price'        => $this->price,
            'status'       => $this->status,
        ]);

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: $this->isEditMode ? 'Room updated.' : 'Room added.', type: 'success');
    }

    public function delete(int $id): void
    {
        Room::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Room deleted.', type: 'success');
    }

    private function resetFields(): void
    {
        $this->roomId      = null;
        $this->room_number = '';
        $this->room_type_id = '';
        $this->price       = '';
        $this->status      = 'Available';
        $this->isEditMode  = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $rooms = Room::with(['roomType', 'latestHousekeeping', 'activeMaintenanceTickets'])
            ->where(function ($q) {
                $q->where('room_number', 'like', "%{$this->search}%")
                  ->orWhere('status', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return $this->view([
            'rooms'     => $rooms,
            'roomTypes' => RoomType::all(),
        ]);
    }
};
