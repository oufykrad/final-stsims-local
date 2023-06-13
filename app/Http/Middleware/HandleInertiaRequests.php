<?php

namespace App\Http\Middleware;

// use App\Models\College;
// use App\Models\Course;
use App\Models\ListPrivilege;
use App\Models\Setting;
use App\Models\ListProgram;
use App\Models\ListDropdown;
use App\Models\ListAgency;
use App\Models\ListStatus;
use App\Models\LocationRegion;
use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Http\Resources\UserResource;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $agency_id = Setting::first()->pluck('agency_id');
        $agency = ListAgency::with('region')->where('id',$agency_id[0])->first();
        $region_code = $agency->region_code;

        return array_merge(parent::share($request), [
            'auth' => (\Auth::check()) ? new UserResource(\Auth::user()) : '',
            'role' => (\Auth::check()) ? \Auth::user()->role : '',
            'flash' => [
                'message' => session('message'),
                'datares' => session('data'),
                'type' => session('type')
            ],
            'regions' => LocationRegion::all(),
            'dropdowns' => ListDropdown::all(),
            'programs' => ListProgram::all(),
            'statuses' => ListStatus::all(),
            // 'expenses' => ListExpense::all(),
            'privileges' => ListPrivilege::all(),
            // 'colleges' => College::all(),
            // 'courses' => Course::all(),
            // 'dropdowns' => Dropdown::all(),
            'region_code' => $region_code,
            'agencies' => ListAgency::all()
        ]);
    }
}
