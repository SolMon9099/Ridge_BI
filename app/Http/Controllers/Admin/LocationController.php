<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Location;
use App\Http\Requests\Admin\LocationRequest;
use App\Service\LocationService;
use App\Service\AccountService;
use Illuminate\Support\Facades\Auth;

class LocationController extends AdminController
{
    public function index()
    {
        $location_query = Location::query();
        if (Auth::guard('admin')->user()->contract_no != null) {
            $location_query = $location_query->where('contract_no', Auth::guard('admin')->user()->contract_no);
        }
        $locations = $location_query->paginate($this->per_page);
        $admins = AccountService::getAllAccountNames();

        return view('admin.location.index')->with([
            'locations' => $locations,
            'admins' => $admins,
        ]);
    }

    public function edit(Request $request, Location $location)
    {
        $login_user = Auth::guard('admin')->user();
        $owners = Admin::where('authority_id', 2)->get();
        $query = Admin::where('authority_id', config('const.authorities_codes.manager'));
        if ($login_user->contract_no != null) {
            $query->where('contract_no', $login_user->contract_no);
        }
        $managers = $query->get();

        return view('admin.location.edit')->with([
            'location' => $location,
            'owners' => $owners,
            'managers' => $managers,
        ]);
    }

    public function create()
    {
        $login_user = Auth::guard('admin')->user();
        $owners = Admin::where('authority_id', 2)->get();
        $query = Admin::where('authority_id', config('const.authorities_codes.manager'));
        if ($login_user->contract_no != null) {
            $query->where('contract_no', $login_user->contract_no);
        }
        $managers = $query->get();

        return view('admin.location.create')->with([
            'owners' => $owners,
            'managers' => $managers,
        ]);
    }

    public function store(LocationRequest $request)
    {
        if (LocationService::doCreate($request)) {
            $request->session()->flash('success', '?????????????????????');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '??????????????????????????????');

            return redirect()->route('admin.location');
        }
    }

    public function update(LocationRequest $request, Location $location)
    {
        if (LocationService::doUpdate($request, $location)) {
            $request->session()->flash('success', '?????????????????????');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '??????????????????????????????');

            return redirect()->route('admin.location');
        }
    }

    public function delete(Request $request, Location $location)
    {
        if (LocationService::doDelete($location)) {
            $request->session()->flash('success', '??????????????????????????????');

            return redirect()->route('admin.location');
        } else {
            $request->session()->flash('error', '????????????????????????????????????');

            return redirect()->route('admin.location');
        }
    }
}
