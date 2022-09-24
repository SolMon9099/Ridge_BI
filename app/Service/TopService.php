<?php

namespace App\Service;

use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\TopBlock;
use App\Models\SearchOption;

class TopService
{
    public static function search()
    {
        $query = TopBlock::query();
        $query->where('user_id', Auth::guard('admin')->user()->id);

        return $query;
    }

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
                if (isset($params['contract_no']) && $params['contract_no'] != '') {
                    DB::table('admins')->where('contract_no', $params['contract_no'])->update(['safie_user_name' => $params['safie_user_name']]);
                }
            }
            if (isset($params['safie_password'])) {
                $cur_Account->safie_password = $params['safie_password'];
                if (isset($params['contract_no']) && $params['contract_no'] != '') {
                    DB::table('admins')->where('contract_no', $params['contract_no'])->update(['safie_password' => $params['safie_password']]);
                }
            }
            if (isset($params['safie_client_id'])) {
                $cur_Account->safie_client_id = $params['safie_client_id'];
                if (isset($params['contract_no']) && $params['contract_no'] != '') {
                    DB::table('admins')->where('contract_no', $params['contract_no'])->update(['safie_client_id' => $params['safie_client_id']]);
                }
            }
            if (isset($params['safie_client_secret'])) {
                $cur_Account->safie_client_secret = $params['safie_client_secret'];
                if (isset($params['contract_no']) && $params['contract_no'] != '') {
                    DB::table('admins')->where('contract_no', $params['contract_no'])->update(['safie_client_secret' => $params['safie_client_secret']]);
                }
            }
            if (isset($params['headers']) && count($params['headers']) > 0) {
                $cur_Account->header_menu_ids = implode(',', $params['headers']);
                if (isset($params['contract_no']) && $params['contract_no'] != '') {
                    DB::table('admins')->where('contract_no', $params['contract_no'])->update(['header_menu_ids' => implode(',', $params['headers'])]);
                }
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

    public static function doDelete($top)
    {
        if (is_object($top)) {
            return $top->delete();
        } else {
            abort(403);
        }
    }

    public static function save_search_option($params)
    {
        $page_name = $params['page_name'];
        $options = $params['search_params'];
        $search_option_record = SearchOption::query()->where('page_name', $page_name)->where('user_id', Auth::guard('admin')->user()->id)->get()->first();
        if ($search_option_record != null) {
            $search_option_record->options = json_encode($options);
            $search_option_record->save();
        } else {
            $search_option_record = new SearchOption();
            $search_option_record->user_id = Auth::guard('admin')->user()->id;
            $search_option_record->options = json_encode($options);
            $search_option_record->page_name = $page_name;
            $search_option_record->save();
        }
    }
}
