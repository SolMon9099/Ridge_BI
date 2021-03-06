<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Http\Requests\Admin\AccountRequest;
use App\Service\AccountService;
use Illuminate\Support\Facades\Auth;

class AccountController extends AdminController
{
    public function index()
    {
        $login_user = Auth::guard('admin')->user();
        if ($login_user->authority_id == config('const.super_admin_code')) {
            $admins = Admin::query()
                ->where(function ($q) {
                    $q->orWhere('is_main_admin', 1);
                    $q->orWhere('authority_id', config('const.super_admin_code'));
                })
                ->paginate($this->per_page);
        } else {
            $admins = Admin::query()->where('contract_no', $login_user->contract_no)->paginate($this->per_page);
        }

        return view('admin.account.index')->with([
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        return view('admin.account.create');
    }

    public function store(AccountRequest $request)
    {
        if (AccountService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.account');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.account');
        }
    }

    public function edit(Request $request, Admin $admin)
    {
        return view('admin.account.edit')->with([
            'admin' => $admin,
        ]);
    }

    public function update(AccountRequest $request, Admin $admin)
    {
        if (AccountService::doUpdate($request, $admin)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.account');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.account');
        }
    }

    public function delete(Request $request, Admin $admin)
    {
        if (AccountService::doDelete($admin)) {
            $request->session()->flash('success', 'アカウントを削除しました。');

            return redirect()->route('admin.account');
        } else {
            $request->session()->flash('error', 'アカウント削除が失敗しました。');

            return redirect()->route('admin.account');
        }
    }
}
