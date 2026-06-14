<?php

namespace App\Repositories;

use App\Models\Reservation;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function getAllPaginated($search, $perPage = 10)
    {
        return Reservation::with(['guest', 'room'])
            ->whereHas('guest', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('room', function($q) use ($search) {
                $q->where('room_number', 'like', '%' . $search . '%');
            })
            ->paginate($perPage);
    }

    public function findById($id)
    {
        return Reservation::findOrFail($id);
    }

    public function createOrUpdate($id, array $data)
    {
        return Reservation::updateOrCreate(['id' => $id], $data);
    }

    public function delete($id)
    {
        return Reservation::destroy($id);
    }

    public function getEventsByDateRange($start, $end)
    {
        return Reservation::with(['guest', 'room'])
            ->whereBetween('check_in_date', [$start, $end])
            ->orWhereBetween('check_out_date', [$start, $end])
            ->get();
    }
}
