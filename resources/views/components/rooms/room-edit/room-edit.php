<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public Room $room;
    public string $room_number = '';
    public string $room_type_id = '';
    public string $price = '';
    public string $status = 'Available';

    public function boot(): void
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function mount(Room $room): void
    {
        $this->room = $room;
        $this->room_number = $room->room_number;
        $this->room_type_id = (string) $room->room_type_id;
        $this->price = (string) $room->price;
        $this->status = $room->status;
    }

    public function save(): void
    {
        $this->validate([
            'room_number'  => 'required|unique:rooms,room_number,' . $this->room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'price'        => 'required|numeric|min:0',
            'status'       => 'required|in:Available,Occupied,Reserved,Maintenance',
        ]);

        $this->room->update([
            'room_number'  => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'price'        => $this->price,
            'status'       => $this->status,
        ]);

        session()->flash('toast', ['message' => 'Room updated successfully!', 'type' => 'success']);
        $this->redirect(route('rooms.index'), navigate: true);
    }

    public function render(): mixed
    {
        return $this->view(['roomTypes' => RoomType::all()]);
    }
};
