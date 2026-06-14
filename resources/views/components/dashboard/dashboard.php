<?php

use Livewire\Component;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\CheckIn;
use App\Models\CheckOut;
use App\Models\Housekeeping;
use Carbon\Carbon;

new class extends Component
{
    public function render(): mixed
    {
        $totalRooms       = Room::count();
        $occupiedRooms    = Room::where('status', 'Occupied')->count();
        $availableRooms   = Room::where('status', 'Available')->count();
        $reservedRooms    = Room::where('status', 'Reserved')->count();
        $checkInsToday    = CheckIn::whereDate('checkin_datetime', Carbon::today())->count();
        $checkOutsToday   = CheckOut::whereDate('checkout_datetime', Carbon::today())->count();
        $revenueToday     = CheckOut::whereDate('checkout_datetime', Carbon::today())->sum('total_amount');
        $occupancyPercent = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        $housekeepingPending = Housekeeping::where('status', '!=', 'Clean')->count();

        $recentReservations = Reservation::with(['guest', 'room'])
            ->latest()
            ->limit(8)
            ->get();

        return $this->view([
            'totalRooms'          => $totalRooms,
            'occupiedRooms'       => $occupiedRooms,
            'availableRooms'      => $availableRooms,
            'reservedRooms'       => $reservedRooms,
            'checkInsToday'       => $checkInsToday,
            'checkOutsToday'      => $checkOutsToday,
            'revenueToday'        => $revenueToday,
            'occupancyPercent'    => $occupancyPercent,
            'housekeepingPending' => $housekeepingPending,
            'recentReservations'  => $recentReservations,
        ]);
    }
};
