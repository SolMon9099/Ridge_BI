<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Location;
use App\Http\Requests\Admin\LocationRequest;
use App\Service\LocationService;
use App\Service\AccountService;

class LocationController extends AdminController
{
    public function index()
    {
        $locations = Location::paginate($this->per_page);
        $admins = AccountService::getAllAccountNames();

        return view('admin.location.index')->with([
            'locations' => $locations,
            'admins' => $admins,
        ]);
    }

    public function edit(Request $request, Location $location)
    {
        $owners = Admin::where('authority_id', 2)->get();
        $managers = Admin::where('authority_id', 3)->get();

        return view('admin.location.edit')->with([
            'location' => $location,
            'owners' => $owners,
            'managers' => $managers,
        ]);
    }

    public function create()
    {
        $owners = Admin::where('authority_id', 2)->get();
        $managers = Admin::where('authority_id', 3)->get();

        return view('admin.location.create')->with([
            'owners' => $owners,
            'managers' => $managers,
        ]);
    }

    public function store(LocationRequest $request)
    {
        if (LocationService::doCreate($request)) {
            $request->session()->flash('success', '登録しました。');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '登録に失敗しました。');

            return redirect()->route('admin.location');
        }
    }

    public function update(LocationRequest $request, Location $location)
    {
        if (LocationService::doUpdate($request, $location)) {
            $request->session()->flash('success', '変更しました。');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '変更に失敗しました。');

            return redirect()->route('admin.location');
        }
    }

    public function delete(Request $request, Location $location)
    {
        if (LocationService::doDelete($location)) {
            $request->session()->flash('success', '現場を削除しました。');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '現場削除が失敗しました。');

            return redirect()->route('admin.location');
        }
    }
}
