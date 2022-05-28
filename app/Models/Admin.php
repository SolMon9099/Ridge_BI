<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'department',
        'is_enabled',
        'authority_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /*public function routeNotificationForMail() {
        return $this->email;
    }*/

    public function authority() {
        return $this->belongsTo('App\Models\Authority', 'authority_id');
    }
}
