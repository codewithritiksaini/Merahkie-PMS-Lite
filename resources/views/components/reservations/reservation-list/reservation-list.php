<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Services\ReservationService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showDrawer = false;
    public bool $isEditMode = false;

    public ?int $reservationId = null;
    public string $guest_id = '', $room_id = '', $check_in_date = '', $check_out_date = '';
    public int $adults = 1, $children = 0;
    public string $special_notes = '', $status = 'Confirmed';

    public function updatedSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $res = Reservation::findOrFail($id);
        $this->reservationId  = $res->id;
        $this->guest_id       = (string)$res->guest_id;
        $this->room_id        = (string)$res->room_id;
        $this->check_in_date  = $res->check_in_date;
        $this->check_out_date = $res->check_out_date;
        $this->adults         = $res->adults;
        $this->children       = $res->children;
        $this->special_notes  = $res->special_notes ?? '';
        $this->status         = $res->status;
        $this->isEditMode     = true;
        $this->showDrawer     = true;
    }

    public function store(ReservationService $service): void
    {
        $this->validate([
            'guest_id'       => 'required|exists:guests,id',
            'room_id'        => 'required|exists:rooms,id',
            'check_in_date'  => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'adults'         => 'required|integer|min:1',
            'children'       => 'required|integer|min:0',
            'status'         => 'required|in:Confirmed,Checked-In,Checked-Out,Cancelled',
        ]);

        if (!$this->isEditMode) {
            $room = Room::find($this->room_id);
            if ($room && $room->status !== 'Available') {
                $this->addError('room_id', 'This room is not available.');
                return;
            }
        }

        $service->saveReservation($this->reservationId, [
            'guest_id'       => $this->guest_id,
            'room_id'        => $this->room_id,
            'check_in_date'  => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'adults'         => $this->adults,
            'children'       => $this->children,
            'special_notes'  => $this->special_notes,
            'status'         => $this->status,
        ], $this->isEditMode);

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Reservation saved successfully!', type: 'success');
    }

    public function delete(int $id, ReservationService $service): void
    {
        $service->deleteReservation($id);
        $this->dispatch('toast', message: 'Reservation deleted.', type: 'success');
    }

    public function checkIn(int $id, ReservationService $service): void
    {
        try {
            $service->processCheckIn($id, Auth::id());
            $this->dispatch('toast', message: 'Guest checked in successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    public function checkOut(int $id, ReservationService $service): void
    {
        try {
            $service->processCheckOut($id);
            $this->dispatch('toast', message: 'Guest checked out successfully!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');
        }
    }

    private function resetFields(): void
    {
        $this->reservationId  = null;
        $this->guest_id       = '';
        $this->room_id        = '';
        $this->check_in_date  = '';
        $this->check_out_date = '';
        $this->adults         = 1;
        $this->children       = 0;
        $this->special_notes  = '';
        $this->status         = 'Confirmed';
        $this->isEditMode     = false;
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $service = App::make(ReservationService::class);
        $reservations = $service->getPaginatedReservations($this->search);
        $guests = Guest::orderBy('name')->get();
        $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets'])
            ->where('status', 'Available')
            ->orWhere('id', $this->room_id)
            ->orderBy('room_number')
            ->get();

        return $this->view(compact('reservations', 'guests', 'rooms'));
    }
};
