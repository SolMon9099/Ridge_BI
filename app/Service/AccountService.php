<?php

namespace App\Service;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        $new_user->contract_no = $params['contract_no'];
        if (Auth::guard('admin')->user()->authority_id == config('const.super_admin_code')) {
            $new_user->is_main_admin = 1;
            if (isset($params['safie_user_name'])) {
                $new_user->safie_user_name = $params['safie_user_name'];
            }
            if (isset($params['safie_password'])) {
                $new_user->safie_password = $params['safie_password'];
            }
            if (isset($params['safie_client_id'])) {
                $new_user->safie_client_id = $params['safie_client_id'];
            }
            if (isset($params['safie_client_secret'])) {
                $new_user->safie_client_secret = $params['safie_client_secret'];
            }
        }
        if (isset($params['headers']) && count($params['headers']) > 0) {
            $new_user->header_menu_ids = implode(',', $params['headers']);
        }
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
            if (isset($params['authority_id'])) {
                $cur_Account->authority_id = $params['authority_id'];
            }
            if (isset($params['contract_no'])) {
                $cur_Account->contract_no = $params['contract_no'];
            }
            if (isset($params['safie_user_name'])) {
                $cur_Account->safie_user_name = $params['safie_user_name'];
            }
            if (isset($params['safie_password'])) {
                $cur_Account->safie_password = $params['safie_password'];
            }
            if (isset($params['safie_client_id'])) {
                $cur_Account->safie_client_id = $params['safie_client_id'];
            }
            if (isset($params['safie_client_secret'])) {
                $cur_Account->safie_client_secret = $params['safie_client_secret'];
            }
            if (isset($params['headers']) && count($params['headers']) > 0) {
                $cur_Account->header_menu_ids = implode(',', $params['headers']);
            }
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

    public static function getAdminUserByContract($contract_no)
    {
        return Admin::query()->where('contract_no', $contract_no)->where('is_main_admin', 1)->get()->first();
    }
}
