<?php

namespace App\Http\Traits;

use Hashids\Hashids;  
use App\Models\Scholar;
use App\Models\SchoolSemester;
use App\Models\ScholarCourse;
use App\Models\ScholarEducation;
use App\Models\ScholarEnrollmentList;
use App\Models\ScholarCourseProspectus;
use App\Models\ListPrivilege;
use App\Models\ListStatus;
use Carbon\Carbon;
use App\Http\Resources\DefaultResource;
use App\Models\Enrollee;
use App\Models\ScholarBenefit;

trait EnrollmentTrait {
   
    public function createList($id,$request) 
    {
        $lists = json_decode($request->lists,true);
        foreach($lists as $key=>$list){
            $options = [];
            ScholarEnrollmentList::create(array_merge($list,[
                'options' => json_encode($options) ,
                'enrollment_id' => $id,
                'encoded_by' =>  \Auth::user()->id
            ]));
        }
    }

    public function check($request)
    {
        $count = ScholarCourse::where('scholar_id',$request->scholar_id)->count();
        if($count == 0){
            $pros = SchoolCourseProspectus::where('school_course_id',$request->subcourse)->first();
            $information = [
                'prospectus' => json_decode($pros->subjects)
            ];

            $data = new ScholarCourse;
            $data->scholar_id = $request->scholar_id;
            $data->subcourse_id = $request->subcourse;
            $data->information = json_encode($information);
            $data->save();
        }
    }

    public function level($request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);
        $data = ScholarEducation::where('scholar_id',$scholar_id[0])->first();
        ($data->level_id != $request->level_id) ? $data->level_id = $request->level_id : '';
        $data->save();
    }

    public function newFinancialGroup($request){
        $hashids = new Hashids('krad',10);
        $scholar_id = $hashids->decode($request->scholar_id);

        $semester_id = $request->semester_id;
        $semester = SchoolSemester::where('id',$semester_id)->first();

        $month = $semester->start_at;

        $data = new Enrollee;
        $data->school_semester_id = $semester_id;
        $data->scholar_id = $scholar_id[0];
        if($data->save()){
            // $list_benefits = ListPrivilege::whereNotIn('name',['Tuition & Other School Fees','Thesis Allowance','Transportation Allowance','Graduation Allowance','Group Accident Insurance', 'Others'])->get();
            $list_benefits = ListPrivilege::where('is_reimburseable',0)->get();
            $type = ScholarEducation::with('school.term')->where('scholar_id',$scholar_id[0])->first();
            $type = $type->school->term->name;
            $others = $data->semester->semester->others;

            switch($type){
                case 'Semester': 
                    $div = 2;
                break;
                case 'Trimester':
                    $div = 3;
                break;
                case 'Quarter Term':
                    $div = 4;
                break;
            }
    
            foreach($list_benefits as $benefit){
                $attachment = [];
                $amount = ($others != 'summer') ? $benefit['regular_amount'] : $benefit['summer_amount'];
                if($others != 'summer'){
                    $total = $amount / (($benefit['type'] == 'Term') ? $div : 1);
                }else{
                    $total = $amount;
                }
                $wew = [
                    'benefit_id' => $benefit['id'],
                    'scholar_id' => $scholar_id[0],
                    'amount' => $total,
                    'release_type' => 'Full',
                    'month' => $month,
                    'status_id' => 11,
                    'school_semester_id' => $semester_id,
                    'attachment' => json_encode($attachment)
                ];
    
                if($benefit['id'] == 1){
                    $number = ($others != 'summer') ? 5 : 2; 
                    for($x = 0; $x < $number; $x++){
                        $list = ScholarBenefit::create($wew);
                        $wew['month'] = date('Y-m-d', strtotime($wew['month']. ' + 1 months'));
                    }
                }else if($benefit['name'] == 'Clothing Allowance'){
                    $count = ScholarBenefit::where('scholar_id',$scholar_id[0])->where('benefit_id',$benefit['id'])->count();
                    ($count == 0) ? $list = ScholarBenefit::create($wew) : '';
                }else{
                    $list = ScholarBenefit::create($wew);
                }
            }
        }
    }
}