<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\Authority;
use App\Models\PageGroup;
use App\Models\AuthorityGroup;

class TopController extends AdminController
{
    public function index()
    {
        return view('admin.top.index');
    }

    public function permission_group() {
        $authority_groups = array();
        $authorities = Authority::all();
        $page_groups = PageGroup::all();

        foreach($authorities as $authority) {
            foreach($page_groups as $page_group) {
                $value = AuthorityGroup::where([
                    'authority_id' => $authority->id,
                    'group_id' => $page_group->id
                ])->first();
                if ($value && $value->access_flag) {
                    $authority_groups[$authority->id][$page_group->id] = 1;
                } else {
                    $authority_groups[$authority->id][$page_group->id] = 0;
                }
            }
        }

        return view('admin.top.permission_group')->with([
            'authorities' => $authorities,
            'page_groups' => $page_groups,
            'authority_groups' => $authority_groups
        ]);
    }

    public function permission_store(Request $request) {
        $authorities = Authority::all();
        $page_groups = PageGroup::all();
        foreach ($authorities as $authority) {
            foreach ($page_groups as $page_group) {
                $value = $request['checkbox'.$authority->id."_".$page_group->id];
                $authority_group = AuthorityGroup::where([
                    'authority_id' => $authority->id,
                    'group_id' => $page_group->id
                ])->first();
                if ($authority->id == 1) $value = 1;
                if ($authority_group) {
                    if ($value) {
                        $authority_group->access_flag = 1;
                    } else {
                        $authority_group->access_flag = 0;
                    }
                    $authority_group->save();
                } else {
                    if ($value) {
                        AuthorityGroup::create([
                            'authority_id' => $authority->id,
                            'group_id' => $page_group->id,
                            'access_flag' => 1
                        ]);
                    } else {
                        AuthorityGroup::create([
                            'authority_id' => $authority->id,
                            'group_id' => $page_group->id,
                            'access_flag' => 0
                        ]);
                    }
                }
            }
        }
        $request->session()->flash('success', '登録しました。');
        return redirect()->route('admin.top.permission_group');
    }
}
