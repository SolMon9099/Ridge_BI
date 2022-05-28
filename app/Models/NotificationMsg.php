<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotificationMsg extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
