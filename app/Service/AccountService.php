<?php

namespace App\Service;

use App\Models\Admin;
use Hash;
use Auth;

class AccountService
{
    public static function doCreate($params)
    {
        $new_user = new Admin();
        $new_user->email = $params['email'];
        $new_user->name = $params['name'];
        $new_user->department = $params['department'];
        $new_user->email = $params['email'];
        $new_user->authority_id = $params['authority_id'];
        $new_user->password = Hash::make($params['password']);
        $new_user->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;

        $new_user->created_by = Auth::guard('admin')->user()->id;
        $new_user->updated_by = Auth::guard('admin')->user()->id;

        return $new_user->save();
    }

    public static function doUpdate($params, $cur_Account)
    {
        if (is_object($cur_Account)) {
            $cur_Account->email = $params['email'];
            $cur_Account->name = $params['name'];
            $cur_Account->department = $params['department'];
            $cur_Account->is_enabled = isset($params['is_enabled']) ? $params['is_enabled'] : 1;
            $cur_Account->authority_id = $params['authority_id'];
            $cur_Account->updated_by = Auth::guard('admin')->user()->id;
            if (isset($params['password']) && $params['password']) {
                $cur_Account->password = Hash::make($params['password']);
            }

            return $cur_Account->save();
        } else {
            abort(403);
        }
    }

    public static function doDelete($cur_Account)
    {
        if (is_object($cur_Account)) {
            return $cur_Account->delete();
        } else {
            abort(403);
        }
    }

    public static function getAllAccountNames()
    {
        $admins = Admin::orderBy('id', 'asc')->get();
        $admins_array = [];
        foreach ($admins as $admin_user) {
            $admins_array[$admin_user->id] = $admin_user->name;
        }

        return $admins_array;
    }

    public static function getUserInfoById($id)
    {
        return Admin::find($id);
    }

    public static function getAccountName($id)
    {
        $admin = Admin::find($id);
        if (is_object($admin)) {
            return $admin->name;
        } else {
            return '';
        }
    }
}
