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
        'contract_no',
        'header_menu_ids',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /*public function routeNotificationForMail() {
        return $this->email;
    }*/

    public function getManagerAllowedPagesAttribute()
    {
        $res = [];
        $allowed_pages = AuthorityGroup::query()->where('authority_id', config('const.authorities_codes.manager'))->where('access_flag', 1)->get()->all();
        foreach ($allowed_pages as $item) {
            $res[] = config('const.page_route_names')[$item->group_id];
        }

        return $res;
    }
}
