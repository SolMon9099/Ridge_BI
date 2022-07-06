<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\NotificationGroupRequest;
use App\Http\Requests\Admin\NotificationMsgRequest;
use App\Service\NotificationGroupService;
use App\Service\NotificationMsgService;
use App\Models\NotificationGroup;
use App\Models\NotificationMsg;

class NotificationController extends AdminController
{
    public function index(Request $request)
    {
        $groups = NotificationGroup::paginate($this->per_page);
        $msgs = NotificationMsg::paginate($this->per_page);

        return view('admin.notification.index')->with([
            'groups' => $groups,
            'msgs' => $msgs,
            'active_tab' => $request->active_tab,
        ]);
    }

    public function edit(Request $request, NotificationGroup $group)
    {
        return view('admin.notification.edit')->with([
            'group' => $group,
        ]);
    }

    public function create()
    {
        return view('admin.notification.create');
    }

    public function store(NotificationGroupRequest $request)
    {
        if (NotificationGroupService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.notification');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.notification');
        }
    }

    public function update(NotificationGroupRequest $request, NotificationGroup $group)
    {
        if (NotificationGroupService::doUpdate($request, $group)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.notification');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.notification');
        }
    }

    public function delete(Request $request, NotificationGroup $group)
    {
        if (NotificationGroupService::doDelete($group)) {
            $request->session()->flash('success', 'グループを削除しました。');

            return redirect()->route('admin.notification');
        } else {
            $request->session()->flash('error', 'グループの削除に失敗しました。');

            return redirect()->route('admin.notification');
        }
    }

    public function create_msg()
    {
        return view('admin.notification.create_msg');
    }

    public function store_msg(NotificationMsgRequest $request)
    {
        if (NotificationMsgService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        }
    }

    public function edit_msg(Request $request, NotificationMsg $msg)
    {
        return view('admin.notification.edit_msg')->with([
            'msg' => $msg,
        ]);
    }

    public function update_msg(NotificationMsgRequest $request, NotificationMsg $msg)
    {
        if (NotificationMsgService::doUpdate($request, $msg)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        }
    }

    public function delete_msg(Request $request, NotificationMsg $msg)
    {
        if (NotificationMsgService::doDelete($msg)) {
            $request->session()->flash('success', '通知メッセージを削除しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        } else {
            $request->session()->flash('error', '通知メッセージの削除に失敗しました。');

            return redirect()->route('admin.notification', ['active_tab' => 'tab2']);
        }
    }
}
