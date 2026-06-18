<?php

use Livewire\Component;
use App\Models\Reservation;

new class extends Component
{
    public function getEvents(): array
    {
        return Reservation::with(['guest', 'rooms'])
            ->whereIn('status', ['Confirmed', 'Checked-In', 'Reserved', 'Checked-Out'])
            ->get()
            ->map(fn ($r) => [
                'id'    => $r->id,
                'title' => (optional($r->guest)->name ?? 'Guest') . ' — Room ' . ($r->rooms->pluck('room_number')->implode(', ') ?: '?'),
                'start' => $r->check_in_date,
                'end'   => $r->check_out_date,
                'color' => match($r->status) {
                    'Checked-In' => '#10b981',
                    'Confirmed'  => '#6366f1',
                    'Reserved'   => '#f59e0b',
                    default      => '#94a3b8',
                },
                'extendedProps' => [
                    'status' => $r->status,
                    'guest'  => optional($r->guest)->name ?? '',
                    'room'   => $r->rooms->pluck('room_number')->implode(', '),
                ],
            ])
            ->toArray();
    }

    public function render(): mixed
    {
        return $this->view(['events' => $this->getEvents()]);
    }
};
