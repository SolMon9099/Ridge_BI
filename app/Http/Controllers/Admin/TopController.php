<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AuthorityGroup;
use Illuminate\Support\Facades\Auth;
use App\Service\DangerService;

class TopController extends AdminController
{
    public function index()
    {
        $danger_detections = DangerService::searchDetections(null)->limit(10)->get()->all();

        return view('admin.top.index')->with([
            'danger_detections' => $danger_detections,
        ]);
    }

    public function permission_group()
    {
        $login_user = Auth::guard('admin')->user();
        $authority_groups = [];

        if ($login_user->authority_id == config('const.super_admin_code')) {
            $data = AuthorityGroup::all();
        } else {
            $data = AuthorityGroup::query()->where('contract_no', $login_user->contract_no)->get()->all();
        }

        foreach ($data as $item) {
            $authority_groups[$item->authority_id][$item->group_id] = $item->access_flag ? 1 : 0;
        }

        return view('admin.top.permission_group')->with([
            'authority_groups' => $authority_groups,
        ]);
    }

    public function permission_store(Request $request)
    {
        $login_user = Auth::guard('admin')->user();
        if ($login_user->authority_id == config('const.super_admin_code')) {
            $request->session()->flash('error', 'スーパー管理者は権限グループを変更出来ません。');

            return redirect()->route('admin.top.permission_group');
        }
        foreach (config('const.authorities') as $authority_id => $authority) {
            if ($authority_id == config('const.authorities_codes.admin')) {
                continue;
            }
            foreach (config('const.pages') as $details) {
                foreach ($details as $detail) {
                    $authority_group = AuthorityGroup::where([
                        'authority_id' => $authority_id,
                        'group_id' => $detail['id'],
                        'contract_no' => $login_user->contract_no,
                    ])->first();
                    if (isset($request['checkbox'.$authority_id.'_'.$detail['id']])) {
                        $value = 1;
                    } else {
                        $value = 0;
                    }
                    if ($authority_group) {
                        $authority_group->access_flag = $value;
                        $authority_group->save();
                    } else {
                        AuthorityGroup::create([
                            'authority_id' => $authority_id,
                            'group_id' => $detail['id'],
                            'access_flag' => $value,
                            'contract_no' => $login_user->contract_no,
                        ]);
                    }
                }
            }
        }
        $request->session()->flash('success', '登録しました。');

        return redirect()->route('admin.top.permission_group');
    }
}
