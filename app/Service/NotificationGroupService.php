<?php

namespace App\Service;

use App\Models\NotificationGroup;
use Illuminate\Support\Facades\Auth;

class NotificationGroupService
{
    public static function doCreate($params)
    {
        $login_user = Auth::guard('admin')->user();
        $new_group = new NotificationGroup();
        $new_group->name = $params['name'];
        $new_group->emails = implode(',', $params['emails']);
        if ($login_user->authority_id != config('const.super_admin_code')) {
            $new_group->contract_no = $login_user->contract_no;
        }
        $new_group->created_by = Auth::guard('admin')->user()->id;
        $new_group->updated_by = Auth::guard('admin')->user()->id;

        return $new_group->save();
    }

    public static function doUpdate($params, $cur_Group)
    {
        if (is_object($cur_Group)) {
            $cur_Group->name = $params['name'];
            $cur_Group->emails = implode(',', $params['emails']);
            $cur_Group->updated_by = Auth::guard('admin')->user()->id;

            return $cur_Group->save();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function doDelete($cur_Group)
    {
        if (is_object($cur_Group)) {
            return $cur_Group->delete();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function getAllGroupNames()
    {
        $groups = NotificationGroup::orderBy('id', 'asc')->get();
        $groups_array = [];
        foreach ($groups as $group) {
            $groups_array[$group->id] = $group->name;
        }

        return $groups_array;
    }

    public static function getGroupInfoById($id)
    {
        return NotificationGroup::find($id);
    }

    public static function getGroupName($id)
    {
        $group = NotificationGroup::find($id);
        if (is_object($group)) {
            return $group->name;
        } else {
            return '';
        }
    }
}
