<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageGroup extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group_name',
        'detail_name',
        'order_no',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
}
