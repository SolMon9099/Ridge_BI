<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AuthorityGroup;

class AdminPermission
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            $login_user = Auth::guard('admin')->user();
            $super_admin_flag = ($login_user->authority_id == config('const.super_admin_code'));
            $admin_flag = ($login_user->authority_id == config('const.authorities_codes.admin'));
            $headers = isset($login_user->header_menu_ids) ? explode(',', $login_user->header_menu_ids) : [];
            if ($super_admin_flag) {
                return $next($request);
            }
            $cur_route = $request->route()->getName();
            if ($cur_route == 'admin.top' || $cur_route == 'admin.logout') {
                return $next($request);
            }
            if ($admin_flag) {
                foreach (config('const.header_menus_routes') as $header_id => $header_route) {
                    if (strpos($header_route, $cur_route) !== false && !in_array($header_id, $headers)) {
                        abort(403);
                    }
                }
            } else {
                $allowed_pages = AuthorityGroup::query()->where('authority_id', $login_user->authority_id)->where('access_flag', 1)->get()->all();
                if (count($allowed_pages) == 0) {
                    abort(403);
                }
                $check_flag = false;
                foreach ($allowed_pages as $record) {
                    if (strpos($cur_route, config('const.page_route_names')[$record->group_id]) !== false) {
                        $check_flag = true;
                    }
                }
                if ($check_flag != true) {
                    abort(403);
                }
                foreach (config('const.header_menus_routes') as $header_id => $header_route) {
                    if (strpos($header_route, $cur_route) !== false && !in_array($header_id, $headers)) {
                        abort(403);
                    }
                }
            }
        }

        return $next($request);
    }
}
