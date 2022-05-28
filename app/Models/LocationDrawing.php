<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocationDrawing extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function obj_camera_mappings()
    {
        return $this->hasMany(CameraMappingDetail::class, 'drawing_id');
    }
}
