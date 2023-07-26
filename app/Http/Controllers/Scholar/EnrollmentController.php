<?php

namespace App\Http\Controllers\Scholar;

use Hashids\Hashids;
use App\Models\Scholar;
use App\Models\ScholarEnrollment;
use App\Models\ScholarEducation;
use App\Models\ScholarEnrollmentList;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Traits\GradeTrait;
use App\Http\Traits\EnrollmentTrait;
use App\Http\Resources\DefaultResource;
use App\Http\Resources\Scholars\SearchResource;
use App\Http\Resources\Scholars\Info\EnrollmentResource;
use App\Http\Requests\EnrollmentRequest;

class EnrollmentController extends Controller
{
    use GradeTrait, EnrollmentTrait, UploadTrait;

    public function index(Request $request){
        $type = $request->type;
        switch($type){
            case 'search':
                return $this->search($request);
            break;
            default : 
            return inertia('Modules/Enrollments/Index');
        }
    }

    public function search($request){
        $keyword = $request->keyword;

        $data = Scholar::with('profile')
        ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
        ->with('education.school.school','education.school.semesters','education.course','education.level')
        ->with('enrollments')
        ->whereHas('status',function ($query){
            $query->where('type','Ongoing');
        })
        ->when($request->keyword, function ($query, $keyword) {
            $query->whereHas('profile',function ($query) use ($keyword) {
                $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                ->orWhere('spas_id','LIKE','%'.$keyword.'%');
            });
        })->take(5)->get();
        return SearchResource::collection($data);
    }

    public function store(EnrollmentRequest $request){
        $data = \DB::transaction(function () use ($request){
            switch($request->type){
                case 'save': 
                    return new SearchResource($this->save($request));
                break;
                case 'grade': 
                    return new EnrollmentResource($this->saveGrade($request));
                break;
                case 'lock': 
                    return new EnrollmentResource($this->lockGrade($request));
                break;
            }
        });

        switch($request->type){
            case 'save': 
                $message = 'Scholar enrolled successfully. Thanks';
            break;
            case 'grade': 
                $message = 'Scholar grades updated successfully. Thanks';
            break;
            case 'lock': 
                $message = 'Scholar grades locked successfully. Thanks';
            break;
        }

        return back()->with([
            'message' => $message,
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }

    public function save(Request $request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);

        $this->newFinancialGroup($request);
        
        $attachments = [
            'grades' => [],
            'enrollments' => $this->enrollment($request)
        ];
        
        $data = ScholarEnrollment::create(array_merge($request->all(),['scholar_id' => $scholar_id[0],'attachment' => json_encode($attachments), 'added_by' => \Auth::user()->id]));
        $this->level($request);
        $this->createList($data->id,$request);
        // $data = ScholarEnrollment::find($data->id);

        $data = Scholar::with('profile')
        ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
        ->with('education.school.school','education.school.semesters','education.course','education.level')
        ->with('enrollments')
        ->whereHas('status',function ($query){
            $query->where('type','Ongoing');
        })
        ->where('id',$scholar_id[0])->first();
        return $data;
    }

    public function show($id){
        $data = ScholarEnrollmentList::where('enrollment_id',$id)->get();
        return DefaultResource::collection($data);
    }

    public function update(Request $request){
        switch($request->type){
            case 'switch': 
                $this->switch($request);
            break;
        }
    }

    public function switch($request){
        $from = $request->from;
        $to = $request->to;
        $p = ScholarEducation::with('subcourse')->where('scholar_id',$request->scholar_id)->first();
        $years = $p->subcourse->years; 
        $semesters = 3;
        $prospectus = json_decode($p->information,true);
    
        for($x = 0; $x < $years; $x++){
            for($y = 0; $y < $semesters; $y++){
                $result = array_filter($prospectus['prospectus'][$x]['semesters'][$y]['courses'], function($all) use ($from) {
                    return $all['code'] == $from['code'];
                });
                if(!empty($result)){
                    for($z = 0; $z < $years; $z++){
                        for($w = 0; $w < $semesters; $w++){
                            $result2 = array_filter($prospectus['prospectus'][$z]['semesters'][$w]['courses'], function($all) use ($to) {
                                return $all['code'] == $to['code'] && $all['is_lab'] == $to['is_lab'];
                            });
                            if(!empty($result2)){
                                $key = array_keys($result)[0];
                                $key2 = array_keys($result2)[0];
                                $prospectus['prospectus'][$x]['semesters'][$y]['courses'][$key] = $result2[$key2];
                                $prospectus['prospectus'][$z]['semesters'][$w]['courses'][$key2] = $result[$key];
                                break;
                            }
                        }
                    }
                    break;
                }
            }
        }
        $p->information = $prospectus;
        $p->save();   

        return back()->with([
            'message' => 'Subject switched successfully. Thanks',
            'data2' =>  new DefaultResource($p),
            'type' => 'bxs-check-circle'
        ]); 
    }
}
