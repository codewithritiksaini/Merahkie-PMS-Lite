<?php

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
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
    public string $guest_id = '', $check_in_date = '', $check_out_date = '';
    public array $room_ids = [];
    public int $adults = 1, $children = 0;
    public string $special_notes = '', $status = 'Confirmed';
    public string $discount_type = 'Fixed', $discount_value = '0';

    public string $payment_type = 'Cash', $payment_amount = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function updatedCheckInDate(): void { $this->room_ids = []; }

    public function updatedCheckOutDate(): void { $this->room_ids = []; }

    public function openCreate(): void
    {
        $this->resetFields();
        $this->showDrawer = true;
    }

    public function edit(int $id): void
    {
        $this->resetValidation();
        $res = Reservation::with('rooms')->findOrFail($id);
        $this->reservationId  = $res->id;
        $this->guest_id       = (string)$res->guest_id;
        $this->room_ids       = $res->rooms->pluck('id')->all();
        $this->check_in_date  = $res->check_in_date;
        $this->check_out_date = $res->check_out_date;
        $this->adults         = $res->adults;
        $this->children       = $res->children;
        $this->discount_type  = $res->discount_type;
        $this->discount_value = (string)$res->discount_value;
        $this->special_notes  = $res->special_notes ?? '';
        $this->status         = $res->status;
        $this->isEditMode     = true;
        $this->showDrawer     = true;
        $this->payment_type   = 'Cash';
        $this->payment_amount = '';
    }

    public function store(ReservationService $service): void
    {
        $this->validate([
            'guest_id'        => 'required|exists:guests,id',
            'room_ids'        => 'required|array|min:1',
            'room_ids.*'      => 'integer|exists:rooms,id',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'adults'          => 'required|integer|min:1',
            'children'        => 'required|integer|min:0',
            'status'          => 'required|in:Confirmed,Checked-In,Checked-Out,Cancelled',
            'discount_type'   => 'required|in:Fixed,Percentage',
            'discount_value'  => 'nullable|numeric|min:0',
            'payment_type'    => 'required|in:Cash,Card,UPI',
            'payment_amount'  => 'nullable|numeric|min:0',
        ]);

        foreach ($this->room_ids as $roomId) {
            $available = Room::availableBetween(
                    $this->check_in_date,
                    $this->check_out_date,
                    $this->isEditMode ? $this->reservationId : null
                )
                ->where('id', $roomId)
                ->exists();

            if (!$available) {
                $this->addError('room_ids', 'One of the selected rooms is not available for these dates.');
                return;
            }
        }

        // Checked-In / Checked-Out are operational states with side effects (room status,
        // payment gate, invoice creation) — they must only be reached via checkIn()/checkOut(),
        // never set directly through this form (even by editing the dropdown via devtools).
        $allowedTransitions = [
            'Confirmed'   => ['Confirmed', 'Cancelled'],
            'Cancelled'   => ['Cancelled', 'Confirmed'],
            'Checked-In'  => ['Checked-In'],
            'Checked-Out' => ['Checked-Out'],
        ];
        $currentStatus = $this->isEditMode
            ? (Reservation::find($this->reservationId)?->status ?? 'Confirmed')
            : 'Confirmed';

        if (!in_array($this->status, $allowedTransitions[$currentStatus] ?? [$currentStatus])) {
            $this->addError('status', 'Use the Check-In / Check-Out actions to change this status.');
            return;
        }

        $reservation = $service->saveReservation($this->reservationId, [
            'guest_id'       => $this->guest_id,
            'room_ids'       => $this->room_ids,
            'check_in_date'  => $this->check_in_date,
            'check_out_date' => $this->check_out_date,
            'adults'         => $this->adults,
            'children'       => $this->children,
            'discount_type'  => $this->discount_type,
            'discount_value' => $this->discount_value !== '' ? $this->discount_value : 0,
            'special_notes'  => $this->special_notes,
            'status'         => $this->status,
        ], $this->isEditMode);

        if (!$this->isEditMode && $this->payment_amount !== '' && (float) $this->payment_amount > 0) {
            Payment::create([
                'reservation_id' => $reservation->id,
                'amount'         => $this->payment_amount,
                'payment_type'   => $this->payment_type,
                'paid_at'        => now(),
            ]);
        }

        $this->resetFields();
        $this->showDrawer = false;
        $this->dispatch('toast', message: 'Reservation saved successfully!', type: 'success');
    }

    public function addPayment(): void
    {
        if (!$this->reservationId) {
            return;
        }

        $this->validate([
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_type'   => 'required|in:Cash,Card,UPI',
        ]);

        Payment::create([
            'reservation_id' => $this->reservationId,
            'amount'         => $this->payment_amount,
            'payment_type'   => $this->payment_type,
            'paid_at'        => now(),
        ]);

        $this->payment_amount = '';
        $this->dispatch('toast', message: 'Payment recorded successfully!', type: 'success');
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
        $this->room_ids       = [];
        $this->check_in_date  = '';
        $this->check_out_date = '';
        $this->adults         = 1;
        $this->children       = 0;
        $this->discount_type  = 'Fixed';
        $this->discount_value = '0';
        $this->special_notes  = '';
        $this->status         = 'Confirmed';
        $this->isEditMode     = false;
        $this->payment_type   = 'Cash';
        $this->payment_amount = '';
        $this->resetValidation();
    }

    public function render(): mixed
    {
        $service = App::make(ReservationService::class);
        $reservations = $service->getPaginatedReservations($this->search);
        $guests = Guest::orderBy('name')->get();

        $rooms = collect();
        if ($this->check_in_date && $this->check_out_date) {
            $rooms = Room::with(['latestHousekeeping', 'activeMaintenanceTickets', 'roomType'])
                ->availableBetween($this->check_in_date, $this->check_out_date, $this->isEditMode ? $this->reservationId : null)
                ->orderBy('room_number')
                ->get();
        }

        $payments = collect();
        $estimatedTotal = 0;
        $totalPaid = 0;
        $balanceDue = 0;

        if ($this->isEditMode && $this->reservationId) {
            $currentReservation = Reservation::with(['rooms', 'payments'])->find($this->reservationId);
            if ($currentReservation) {
                $payments = $currentReservation->payments->sortByDesc('paid_at')->values();
                $estimatedTotal = $currentReservation->estimated_total;
                $totalPaid = $currentReservation->total_paid;
                $balanceDue = $currentReservation->balance_due;
            }
        } elseif (!empty($this->room_ids) && $this->check_in_date && $this->check_out_date) {
            $preview = new Reservation([
                'check_in_date'  => $this->check_in_date,
                'check_out_date' => $this->check_out_date,
                'discount_type'  => $this->discount_type,
                'discount_value' => $this->discount_value !== '' ? $this->discount_value : 0,
            ]);
            $preview->setRelation('rooms', Room::whereIn('id', $this->room_ids)->get());
            $estimatedTotal = $preview->estimated_total;
            $balanceDue = round($estimatedTotal - (float) ($this->payment_amount !== '' ? $this->payment_amount : 0), 2);
        }

        return $this->view(compact('reservations', 'guests', 'rooms', 'payments', 'estimatedTotal', 'totalPaid', 'balanceDue'));
    }
};
