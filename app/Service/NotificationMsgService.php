<?php

namespace App\Service;

use App\Models\NotificationMsg;
use App\Models\NotificationGroup;
use Illuminate\Support\Facades\Auth;
class NotificationMsgService
{
    public static function doCreate($params)
    {
        $new_msg = New NotificationMsg();
        $new_msg->title = $params['title'];
        $new_msg->content = $params['content'];
        // $new_msg->group_id = $params['group_id'];

        $new_msg->created_by = Auth::guard('admin')->user()->id;
        $new_msg->updated_by =  Auth::guard('admin')->user()->id;
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
