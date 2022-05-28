<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthorityGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'authority_id',
        'group_id',
        'access_flag',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
