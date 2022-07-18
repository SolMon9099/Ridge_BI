<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ThiefDetectionRule extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function getAllByCamera()
    {
        return $this->belongsTo('App\Models\Camera', 'camera_id');
    }
}
