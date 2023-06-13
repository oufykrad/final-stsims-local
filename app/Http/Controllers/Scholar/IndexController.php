<?php

namespace App\Http\Controllers\Scholar;

use App\Models\Scholar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Scholars\IndexResource;
use App\Http\Traits\ScholarUpdate;
use App\Http\Requests\ScholarRequest;

class IndexController extends Controller
{
    use ScholarUpdate;

    public function index(Request $request){
        $type = $request->type;

        switch($type){
            case 'lists':
                return $this->lists($request);
            break;
            case 'counts':
                return [
                    'statistics' => $this->statistics($request),
                    'active' => []
                ];
            break;
            case 'generate':
                return $this->generate($request);
            break;
            default : 
            return inertia('Modules/Scholars/Index');
        }
    }

    public function lists($request){
        $info = (!empty(json_decode($request->info))) ? json_decode($request->info) : NULL;
        $filter = (!empty(json_decode($request->filter))) ? json_decode($request->filter) : NULL;

        $keyword = $info->keyword;

        $data = IndexResource::collection(
            Scholar::
            with('addresses.region','addresses.province','addresses.municipality','addresses.barangay')
            ->with('profile')
            ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
            ->with('education.school.school','education.course','education.level')
            ->whereHas('profile',function ($query) use ($keyword) {
                $query->when($keyword, function ($query, $keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                    ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                    ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                });
            })
            ->whereHas('addresses',function ($query) use ($filter) {
                if(!empty($filter)){
                    (property_exists($filter, 'region')) ? $query->where('region_code',$filter->region)->where('is_permanent',1) : '';
                    (property_exists($filter, 'province')) ? $query->where('province_code',$filter->province)->where('is_permanent',1) : '';
                    (property_exists($filter, 'municipality')) ? $query->where('municipality_code',$filter->municipality)->where('is_permanent',1) : '';
                    (property_exists($filter, 'barangay')) ? $query->where('barangay_code',$filter->barangay)->where('is_permanent',1) : '';
                }
            })
            ->whereHas('education',function ($query) use ($filter) {
                if(!empty($filter)){
                    (property_exists($filter, 'school')) ? $query->where('school_id',$filter->school) : '';
                    (property_exists($filter, 'course')) ? $query->where('course_id',$filter->course) : '';
                }
            })
            ->where(function ($query) use ($info) {
                ($info->program == null) ? '' : $query->where('program_id',$info->program);
                ($info->subprogram == null) ? '' : $query->where('subprogram_id',$info->subprogram);
                ($info->category == null) ? '' : $query->where('category_id',$info->category);
                ($info->status == null) ? '' : $query->where('status_id',$info->status);
                ($info->year == null) ? '' : $query->where('awarded_year',$info->year);
             })
             ->orderBy('awarded_year','ASC')
            ->paginate($info->counts)
            ->withQueryString()
        );
        return $data;
    }

    public function store(ScholarRequest $request){
        $data = \DB::transaction(function () use ($request){
            switch($request->editable){
                case 'update': 
                    return $this->scholar($request);
                break;
                case 'course': 
                    return $this->course($request);
                break;
            }
        });
        return back()->with([
            'message' => 'Scholar updated successfully. Thanks',
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }
}
