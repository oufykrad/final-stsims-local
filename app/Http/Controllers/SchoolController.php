<?php

namespace App\Http\Controllers;

use Hashids\Hashids;
use App\Models\Setting;
use App\Models\SchoolCampus;
use App\Models\SchoolSemester;
use App\Models\SchoolGrading;
use App\Models\SchoolCourseProspectus;
use App\Models\ListCourse;
use Illuminate\Http\Request;
use App\Http\Traits\SchoolTrait;
use App\Http\Resources\DefaultResource;
use App\Http\Requests\SchoolProfileRequest;
use App\Http\Resources\Schools\IndexResource;
use App\Http\Resources\Schools\CourseResource;

class SchoolController extends Controller
{
    use SchoolTrait; 

    public function index(Request $request){
        
        $type = $request->type;
        switch($type){
            case 'lists':
                return $this->lists($request);
            break;
            case 'counts':
                return [
                    'statistics' => $this->statistics($request),
                    'active' => $this->active($request)
                ];
            break;
            case 'generate':
                return $this->generate($request);
            break;
            case 'schools':
                return $this->listschools($request);
            break;
            case 'courses':
                return $this->listcourses($request);
            break;
            default : 
            return inertia('Modules/Schools/Index');
        }
    }

    public function lists($request){
        $data = IndexResource::collection(
            SchoolCampus::query()
            ->with('school.class','term:id,name','grading:id,name')
            ->with('region:region,code','province:name,code','municipality:name,code')
            ->when($request->region, function ($query, $region) {
                $query->where('region_code',$region);
            })
            ->when($request->province, function ($query, $province) {
                $query->where('province_code',$province);
            })
            ->when($request->municipality, function ($query, $municipality) {
                $query->where('municipality_code',$municipality);
            })
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('school',function ($query) use ($keyword) {
                    $query->where('name', 'LIKE', '%'.$keyword.'%');
                })->orWhere(function ($query) use ($keyword) {
                    $query->where('campus', 'LIKE', '%'.$keyword.'%');
                });
            })
            ->whereHas('school',function ($query) {
                $query->orderBy('name','ASC');
            })
            ->paginate($request->counts)
            ->withQueryString()
        );
        return $data;
    }

    public function show($id)
    {
        $hashids = new Hashids('krad',10);
        $id = $hashids->decode($id);
        
        $data = new IndexResource(
            SchoolCampus::with('school')
            ->with('gradings')
            ->with('school.class','term:id,name','grading:id,name')
            ->with('semesters.semester','courses.course')
            ->with('region:region,code','province:name,code','municipality:name,code')
            ->where('id',$id[0])->first()
        );

        return inertia('Modules/Schools/View',[
            'school' => $data
        ]);
    }

    public function edit($id,Request $request){
        $data = \DB::transaction(function () use ($id,$request){
            $type = $request->type;
            switch($type){
                case 'counts':
                    return $this->counts($id);
                break;
                case 'semesters':
                    return $this->semesters($id,$request->counts);
                break;
                case 'semester':
                    return $this->semester($id);
                break;
                case 'courses':
                    return $this->courses($id,$request->counts);
                break;
            }
        });
        return $data;
    }

    public function store(SchoolProfileRequest $request){
        switch($request->option){
            case 'semester': 
                $message = 'Semester successfully created. Thanks';
                $data = new DefaultResource(SchoolSemester::create($request->all()));
                $update = SchoolSemester::where('id','!=',$data->id)->where('school_id',$data->school_id)->update(['is_active' => false]);
            break;
            case 'prospectus': 
                $level = ['First Year','Second Year','Third Year','Fourth Year','Fifth Year'];
                $semester = ['First Semester', 'Second Semester', 'Summer Class'];
                $trimester = ['First Term', 'Second Term', 'Third Term', 'Midyear'];
                $quarter = ['First Term', 'Second Term', 'Third Term','Fourth Term'];
                $years = $request->years;
                $type = $request->type;
                if($type == 'Semester'){
                    $semesters = $semester;
                }else  if($type == 'Trimester'){
                    $semesters = $trimester;
                }else{
                    $semesters = $quarter;
                }
                $group = []; $courses = [];

                for ($i = 0; $i < $years; ++$i) {
                    $sem = [];
                    foreach($semesters as $key=>$semester){
                        $sem[] = ['semester' => $semester,'total' => 0,'courses' => $courses];
                    }
                    $group[] = ['year_level' => $level[$i],'semesters' => $sem];
                }
                $request['subjects'] = json_encode($group,true);
                $request['added_by'] = \Auth::user()->id;
                $data = SchoolCourseProspectus::create($request->all());
                $data = SchoolCourseProspectus::where('id',$data->id)->first();
                $message = 'Prospectus successfully added. Thanks';
            break;
        }
        
        return back()->with([
            'data' => $data,
            'message' => $message,
            'type' => 'bxs-check-circle'
        ]);
    }

    public function update(Request $request){
        if($request->type == 'grading'){
            if($request->editable){
                $data = SchoolGrading::find($request->id);
                $data->update($request->except('editable','type'));
                $message = 'Grade successfully updated. Thanks';
            }else{
                $data = SchoolGrading::create($request->all());
                $message = 'Grade successfully created. Thanks';
            }
            
            return back()->with([
                'data' => $data,
                'message' => $message,
                'type' => 'bxs-check-circle'
            ]);
        }else if($request->type == 'disable'){
            $data = SchoolGrading::where('id',$request->id)->update(['is_active' => $request->is_active]);
            $message = 'Grade successfully updated. Thanks';
            
            return back()->with([
                'data' => $data,
                'message' => $message,
                'type' => 'bxs-check-circle'
            ]);
        }else{
            $data = SchoolCourseProspectus::where('id',$request->id)->first();
            $data->update($request->except('editable'));
            $message = 'Prospectus successfully updated. Thanks';
            
            return back()->with([
                'data' => $data,
                'message' => $message,
                'type' => 'bxs-check-circle'
            ]);
        }
    }

    public function listschools($request){
        $data = SchoolCampus::with('school')->withCount([
        'scholars' => function ($query) use ($request){
            $query->when($request->scholar, function ($query, $scholar) {
                $query->whereHas('scholar',function ($query) use ($scholar) {
                    $query->whereHas('status',function ($query) use ($scholar) {
                        ($scholar == 'ongoing') ? $query->where('type','Ongoing') : $query->where('name','Graduated');
                    });
                });
            });
        }])
        ->when($request->sort, function ($query, $sort) {
            $query->orderBy('scholars_count', $sort);
        })
        ->when($request->scholar, function ($query, $scholar) {
            $query->whereHas('scholars',function ($query) use ($scholar) {
                $query->whereHas('scholar',function ($query) use ($scholar) {
                    $query->whereHas('status',function ($query) use ($scholar) {
                        ($scholar == 'ongoing') ? $query->where('type','Ongoing') : $query->where('name','Graduated');
                    });
                });
            });
        })
        ->paginate(10);
        return DefaultResource::collection($data);
    }

    public function listcourses($request){
        $data = ListCourse::withCount([
        'scholars' => function ($query) use ($request){
            $query->when($request->scholar, function ($query, $scholar) {
                $query->whereHas('scholar',function ($query) use ($scholar) {
                    $query->whereHas('status',function ($query) use ($scholar) {
                        ($scholar == 'ongoing') ? $query->where('type','Ongoing') : $query->where('name','Graduated');
                    });
                });
            });
        }])
        ->when($request->scholar, function ($query, $scholar) {
            $query->whereHas('scholars',function ($query) use ($scholar) {
                $query->whereHas('scholar',function ($query) use ($scholar) {
                    $query->whereHas('status',function ($query) use ($scholar) {
                        ($scholar == 'ongoing') ? $query->where('type','Ongoing') : $query->where('name','Graduated');
                    });
                });
            });
        })
        ->orderBy('scholars_count', $request->sort)
        ->paginate(10);
        return DefaultResource::collection($data);
    }
}
