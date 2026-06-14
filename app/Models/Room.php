<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'room_type_id', 'price', 'status'];

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function housekeeping()
    {
        return $this->hasMany(Housekeeping::class);
    }

    public function latestHousekeeping()
    {
        return $this->hasOne(Housekeeping::class)->latestOfMany();
    }

    public function maintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class);
    }

    public function activeMaintenanceTickets()
    {
        return $this->hasMany(MaintenanceTicket::class)->whereIn('status', ['Open', 'In Progress']);
    }
}
