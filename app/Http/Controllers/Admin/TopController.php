<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AuthorityGroup;

class TopController extends AdminController
{
    public function index()
    {
        return view('admin.top.index');
    }

    public function permission_group()
    {
        $authority_groups = [];

        $data = AuthorityGroup::all();
        foreach ($data as $item) {
            $authority_groups[$item->authority_id][$item->group_id] = $item->access_flag ? 1 : 0;
        }

        return view('admin.top.permission_group')->with([
            'authority_groups' => $authority_groups,
        ]);
    }

    public function permission_store(Request $request)
    {
        foreach (config('const.authorities') as $authority_id => $authority) {
            foreach (config('const.pages') as $details) {
                foreach ($details as $detail) {
                    $authority_group = AuthorityGroup::where([
                        'authority_id' => $authority_id,
                        'group_id' => $detail['id'],
                    ])->first();
                    if (isset($request['checkbox'.$authority_id.'_'.$detail['id']])) {
                        $value = 1;
                    } else {
                        $value = 0;
                        if ($authority_id == 1) {
                            $value = 1;
                        }
                    }
                    if ($authority_group) {
                        $authority_group->access_flag = $value;
                        $authority_group->save();
                    } else {
                        AuthorityGroup::create([
                            'authority_id' => $authority_id,
                            'group_id' => $detail['id'],
                            'access_flag' => $value,
                        ]);
                    }
                }
            }
        }
        $request->session()->flash('success', '登録しました。');

        return redirect()->route('admin.top.permission_group');
    }
}
