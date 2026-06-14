<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Reservation;
use App\Models\Housekeeping;
use Carbon\Carbon;

new class extends Component
{
    public string $date = '';

    public function mount(): void
    {
        $this->date = Carbon::today()->toDateString();
    }

    public function render(): mixed
    {
        $day = Carbon::parse($this->date);

        $totalRooms      = Room::count();
        $occupiedRooms   = Room::where('status', 'Occupied')->count();
        $availableRooms  = Room::where('status', 'Available')->count();
        $checkInsToday   = CheckIn::whereDate('checkin_datetime', $day)->count();
        $checkOutsToday  = CheckOut::whereDate('checkout_datetime', $day)->count();
        $revenueToday    = CheckOut::whereDate('checkout_datetime', $day)->sum('total_amount');
        $occupancyRate   = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        $housekeepingPending = Housekeeping::where('status', '!=', 'Clean')->count();

        $recentCheckOuts = CheckOut::with(['reservation.guest', 'reservation.room'])
            ->whereDate('checkout_datetime', $day)
            ->latest()
            ->limit(10)
            ->get();

        return $this->view(compact(
            'totalRooms', 'occupiedRooms', 'availableRooms',
            'checkInsToday', 'checkOutsToday', 'revenueToday',
            'occupancyRate', 'housekeepingPending', 'recentCheckOuts'
        ));
    }
};
