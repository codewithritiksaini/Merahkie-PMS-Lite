<?php

namespace App\Services;

use App\Repositories\ReservationRepositoryInterface;
use App\Models\Room;

class ReservationService
{
    protected $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function getPaginatedReservations($search)
    {
        return $this->reservationRepository->getAllPaginated($search);
    }

    public function saveReservation($id, $data, $isEditMode)
    {
        if (!$isEditMode) {
            $room = Room::find($data['room_id']);
            if ($room && $room->status === 'Available') {
                $room->update(['status' => 'Reserved']);
            }
        }

        return $this->reservationRepository->createOrUpdate($id, $data);
    }

    public function deleteReservation($id)
    {
        $res = $this->reservationRepository->findById($id);
        if ($res->status != 'Checked-Out' && $res->status != 'Cancelled') {
            $room = Room::find($res->room_id);
            if ($room) {
                $room->update(['status' => 'Available']);
            }
        }
        return $this->reservationRepository->delete($id);
    }

    public function getCalendarEvents($start, $end)
    {
        return $this->reservationRepository->getEventsByDateRange($start, $end);
    }

    public function processCheckIn($reservationId, $userId, $remarks = null)
    {
        $res = $this->reservationRepository->findById($reservationId);
        if ($res->status !== 'Confirmed' && $res->status !== 'Reserved') {
            throw new \Exception('Reservation must be Confirmed to Check-In.');
        }

        // Change Reservation Status
        $res->update(['status' => 'Checked-In']);

        // Change Room Status
        $room = Room::find($res->room_id);
        if ($room) {
            $room->update(['status' => 'Occupied']);
        }

        // Generate Booking Code MK2026001
        $bookingCode = 'MK' . date('Y') . str_pad($reservationId, 3, '0', STR_PAD_LEFT);

        // Create CheckIn record
        return \App\Models\CheckIn::create([
            'reservation_id' => $reservationId,
            'booking_code' => $bookingCode,
            'checkin_datetime' => now(),
            'user_id' => $userId,
            'remarks' => $remarks
        ]);
    }

    public function processCheckOut($reservationId)
    {
        $res = $this->reservationRepository->findById($reservationId);
        if ($res->status !== 'Checked-In') {
            throw new \Exception('Reservation must be Checked-In to Check-Out.');
        }

        $checkIn = \App\Models\CheckIn::where('reservation_id', $reservationId)->first();
        if (!$checkIn) {
            throw new \Exception('No Check-In record found for this reservation.');
        }

        // Calculate nights
        $checkInDate = \Carbon\Carbon::parse($res->check_in_date);
        $checkOutDate = now();
        $nights = $checkInDate->diffInDays($checkOutDate) == 0 ? 1 : $checkInDate->diffInDays($checkOutDate);

        // Calculate charges
        $roomRate = $res->room->price;
        $subtotal = $nights * $roomRate;
        $tax = $subtotal * 0.10; // 10% tax example
        $totalAmount = $subtotal + $tax;

        // Change Reservation Status
        $res->update(['status' => 'Checked-Out']); // 'Completed' conceptually

        // Change Room Status
        $room = Room::find($res->room_id);
        if ($room) {
            $room->update(['status' => 'Available']);
        }

        // Create CheckOut record
        $checkout = \App\Models\CheckOut::create([
            'reservation_id' => $reservationId,
            'checkout_datetime' => $checkOutDate,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total_amount' => $totalAmount
        ]);

        // Create Invoice
        \App\Models\Invoice::create([
            'invoice_number' => 'MK-INV-' . date('Y') . '-' . str_pad($checkout->id, 3, '0', STR_PAD_LEFT),
            'checkout_id' => $checkout->id
        ]);

        return $checkout;
    }
}
