<?php

namespace App\Service;

use App\Models\NotificationMsg;
use Illuminate\Support\Facades\Auth;

class NotificationMsgService
{
    public static function doCreate($params)
    {
        $login_user = Auth::guard('admin')->user();
        $new_msg = new NotificationMsg();
        $new_msg->title = $params['title'];
        $new_msg->content = $params['content'];
        if ($login_user->authority_id != config('const.super_admin_code')) {
            $new_msg->contract_no = $login_user->contract_no;
        }
        // $new_msg->group_id = $params['group_id'];

        $new_msg->created_by = Auth::guard('admin')->user()->id;
        $new_msg->updated_by = Auth::guard('admin')->user()->id;

        return $new_msg->save();
    }

    public static function doUpdate($params, $cur_msg)
    {
        if (is_object($cur_msg)) {
            $cur_msg->title = $params['title'];
            $cur_msg->content = $params['content'];
            $cur_msg->updated_by = Auth::guard('admin')->user()->id;

            return $cur_msg->save();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function doDelete($cur_msg)
    {
        if (is_object($cur_msg)) {
            return $cur_msg->delete();
        } else {
            return redirect()->route('admin.top');
        }
    }

    public static function getMsgInfoById($id)
    {
        return NotificationMsg::find($id);
    }
}
