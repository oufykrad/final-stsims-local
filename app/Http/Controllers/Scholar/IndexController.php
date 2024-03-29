<?php

namespace App\Http\Controllers\Scholar;

use Hashids\Hashids;
use App\Models\Scholar;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\Scholars\IndexResource;
use App\Http\Resources\Scholars\Benefits\ListResource;
use App\Http\Traits\ScholarUpdate;
use App\Http\Requests\ScholarRequest;

class IndexController extends Controller
{
    use ScholarUpdate;

    public function index(Request $request){
        $type = $request->type;

        switch($type){
            case 'ongoing':
                return $this->ongoing($request);
            break;
            case 'lists':
                return $this->lists($request);
            break;
            case 'counts':
                return [
                    'statistics' => $this->statistics($request),
                    'types' => $this->types($request),
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
            ->where(function ($query) use ($info,$filter) {
                ($info->program == null) ? '' : $query->where('program_id',$info->program);
                // ($filter != null) ? ($filter->subprogram == null) ? '' : $query->where('subprogram_id',$filter->subprogram) : '';
                ($info->category == null) ? '' : $query->where('category_id',$info->category);
                ($info->status == null) ? '' : $query->where('status_id',$info->status);
                ($info->year == null) ? '' : $query->where('awarded_year',$info->year);
                ($info->type == null) ? '' : $query->where('is_undergrad',$info->type);
             })
            ->orderBy('awarded_year',$info->sorty)
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

    public function show($data){
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($data);
        
        $data = Scholar::
        with('addresses.region','addresses.province','addresses.municipality','addresses.barangay')
        ->with('profile')
        ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
        ->with('education.school.school','education.course','education.level')
        ->where('id',$id)->first();

        $benefits = [];

        $enrollments = Scholar::with('benefits.benefit')
        ->with('enrollments.semester.semester')->with('enrollments.semester.benefits')->with('enrollments.level')->with('enrollments.lists')
        ->withWhereHas('benefits', function ($query) {
            $query->where('status_id',13);
        })
        ->withWhereHas('enrollments.semester.benefits', function ($query) use ($id) {
            $query->where('scholar_id',$id)->where('status_id',13);
        })
        ->where('id',$id)
        ->first();

        return inertia('Modules/Scholars/Profile/Index',[
            'user' => new IndexResource($data),
            'benefits' => $benefits,
            'enrollments' => new ListResource($enrollments)
        ]);
    }

    public function ongoing($request){
        $keyword = $request->keyword;
        $data = IndexResource::collection(
            Scholar::
            with('addresses.region','addresses.province','addresses.municipality','addresses.barangay')
            ->with('profile')
            ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
            ->with('education.school.school','education.course','education.level')
            ->whereHas('status',function ($query) {
                $query->where('type','ongoing');
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status_id',$status);
            })
            ->whereHas('profile',function ($query) use ($keyword) {
                $query->when($keyword, function ($query, $keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                    ->where(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                    ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                });
            })
            ->orderBy('awarded_year','ASC')
            ->paginate($request->counts)
            ->withQueryString()
        );
        return $data;
    }
}
