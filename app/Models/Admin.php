<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

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
        'is_main_admin',
        'safie_user_name',
        'safie_password',
        'safie_client_id',
        'safie_client_secret',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /*public function routeNotificationForMail() {
        return $this->email;
    }*/

    public function getManagerAllowedPagesAttribute()
    {
        $login_user = Auth::guard('admin')->user();
        $res = [];
        $query = AuthorityGroup::query()->where('authority_id', config('const.authorities_codes.manager'))->where('access_flag', 1);
        if ($login_user->contract_no != null) {
            $query->where('contract_no', $login_user->contract_no);
        }
        $allowed_pages = $query->get()->all();
        foreach ($allowed_pages as $item) {
            $res[] = config('const.page_route_names')[$item->group_id];
        }

        return $res;
    }
}
