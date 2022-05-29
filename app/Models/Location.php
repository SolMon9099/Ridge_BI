<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function obj_location_drawings()
    {
        return $this->hasMany(LocationDrawing::class, 'location_id');
    }

    public function obj_cameras()
    {
        return $this->hasMany(Camera::class, 'location_id');
    }
}
