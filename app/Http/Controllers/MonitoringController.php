<?php

namespace App\Http\Controllers;

use App\Models\Scholar;
use App\Models\Enrollee;
use App\Models\ListStatus;
use App\Models\ListAgency;
use App\Models\ScholarEnrollment;
use App\Models\ScholarBenefit;
use App\Models\SchoolSemester;
use App\Models\SchoolCampus;
use Illuminate\Http\Request;
use App\Http\Resources\MonitoringResource;
use App\Http\Resources\Scholars\Benefits\ListResource;

class MonitoringController extends Controller
{
    public function index(Request $request){
        $type = $request->type;

        switch($type){
            case 'counts':
                $array = [
                    'grades' => $this->grades(),
                    'benefits' => $this->benefits(),
                    'termination' => $this->termination(),
                    'enrolled' => $this->enrolled(),
                    'semesters' => $this->semesters($request),
                    'schools' => $this->schools(),
                    'scholars' => $this->scholars(),
                    'total' => Scholar::count()
                ];
                return $array;
            break;
            case 'statuses':
                return $this->statuses();
            break;
            case 'monitoring':
                return $this->monitoring($request);
            break;
            default : 
            return inertia('Modules/Monitoring/Index');
        }
    }

    public function monitoring($request){
        $scholar_id = $request->id;

        $data = Scholar::with('profile','program')->with('benefits.benefit')
        ->with('enrollments.semester.semester')->with('enrollments.semester.benefits')->with('enrollments.level')->with('enrollments.lists')
        ->withWhereHas('benefits', function ($query) {
            $query->where('status_id',13);
        })
        ->withWhereHas('enrollments.semester.benefits', function ($query) use ($scholar_id) {
            $query->where('scholar_id',$scholar_id)->where('status_id',13);
        })
        ->where('id',$scholar_id)
        ->first();

        return new ListResource($data);
    }

    public function statuses(){
        $data = ListStatus::where('type','ongoing')->withCount('status')->orderBy('status_count', 'desc')->get();
        return $data;
    }

    public function grades(){
        $data = ScholarEnrollment::whereHas('lists',function ($query){
            $query->where('grade',NULL);
        })
        ->whereHas('semester',function ($query){
            $query->where('is_active',0);
        })
        ->whereHas('scholar',function ($query){
            $query ->whereHas('status',function ($query){
                $query->where('type','ongoing');
            });
        })
        ->pluck('scholar_id');
        return $data;
    }

    public function termination(){
        $data = ScholarEnrollment::withCount([
        'lists' => function ($query) {
            $query->where('grade','F')->groupBy('enrollment_id');
        }])
        ->whereHas('scholar',function ($query){
            $query ->whereHas('status',function ($query){
                $query->where('type','ongoing');
            });
        })
        ->having('lists_count', '>', 1)
        ->pluck('scholar_id');
        $scholars = Scholar::with('profile:id,scholar_id,firstname,lastname,middlename')->whereIn('id',$data)->get();
        return MonitoringResource::collection($scholars);
    }

    public function enrollment(){
        $data = ScholarEnrollment::withCount([
        'lists' => function ($query) {
            $query->where('grade','F')->groupBy('enrollment_id');
        }])
        ->whereHas('scholar',function ($query){
            $query ->whereHas('status',function ($query){
                $query->where('type','ongoing');
            });
        })
        ->having('lists_count', '>', 1)
        ->pluck('scholar_id');
        $scholars = Scholar::with('profile:id,scholar_id,firstname,lastname,middlename')->whereIn('id',$data)->get();
        return MonitoringResource::collection($scholars);
    }

    public function benefits(){
        $date = date('Y-m-d');
        $data = ScholarBenefit::whereIn('status_id',[11,12])->where('month','<=',$date)->groupBy('scholar_id')->pluck('scholar_id');
        $scholars = Scholar::with('profile:id,scholar_id,firstname,lastname,middlename')->whereIn('id',$data)->get();
        return MonitoringResource::collection($scholars);
    }

    public function enrolled(){
        $data = Enrollee::whereHas('semester',function ($query){
            $query->where('is_active',1);
        })->pluck('scholar_id');
        return $data;
    }

    public function semesters($request){
        $data = SchoolSemester::where('year',$request->semester_year)->where('is_active',1)->pluck('school_id');
        return $data;
    }

    public function schools(){
        $agency_id = config('app.agency');
        $region = ListAgency::select('region_code')->where('id',$agency_id)->first();
        $region = $region->region_code;

        $data = SchoolCampus::query()->whereHas('municipality',function ($query) use ($region){
            $query->whereHas('province',function ($query) use ($region){
                $query->whereHas('region',function ($query) use ($region){
                    $query->where('code',$region);
                });
            });
        })->count();
        return $data;
    }

    public function scholars(){
        $data = Scholar::whereHas('status',function ($query){
                $query->where('type','Ongoing');
            })->count();
        return $data;
    }
}
