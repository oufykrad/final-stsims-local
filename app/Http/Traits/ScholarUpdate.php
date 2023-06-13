<?php

namespace App\Http\Traits;

use App\Models\User;
use App\Models\Scholar;
use App\Models\ScholarAddress;
use App\Models\ScholarEducation;
use App\Models\ScholarStatus;
use App\Models\ListAgency;
use App\Models\ListProgram;
use App\Models\ListDropdown;
use App\Models\SchoolCourseProspectus;
use App\Http\Resources\Scholars\Info\ReportResource;
use App\Http\Resources\Scholars\IndexResource;
use App\Http\Resources\Scholars\SearchResource;

trait ScholarUpdate { 
    
    public static function scholar($request){
        
        switch($request->type){
            case 'education':
                $data = ScholarEducation::where('id',$request->education_id)->update($request->except('id','education_id','editable','type'));
            break;
            case 'address':
                $data = ScholarAddress::where('id',$request->address_id)->update($request->except('id','address_id','editable','type'));
            break;
            case 'account_no':
                $data = Scholar::where('id',$request->id)->update($request->except('id','editable','type'));
            break;
        }

        $data = Scholar::with('profile')
        ->with('addresses.region','addresses.province','addresses.municipality','addresses.barangay')
        ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
        ->with('education.school.school','education.course')
        ->where('id',$request->id)->first();

        return new IndexResource($data);
    }

    public static function generate($request){
        ini_set('memory_limit', '-1');
        $info = (!empty(json_decode($request->info))) ? json_decode($request->info) : NULL;

        $agency_id = config('app.agency');
        $agency = ListAgency::with('region')->where('id',$agency_id)->first();

     
        $data = Scholar::with('addresses.region','addresses.province','addresses.municipality','addresses.barangay','profile')
        ->with('program')->with('profile')->with('education.school.school','education.course')
        ->where('is_completed',1)
        ->where(function($query) use ($info) {  
            ($info->program == '') ? '' : $query->where('program_id',$info->program);
            ($info->status == '') ? '' : $query->where('status_id',$info->status);
            ($info->from != '' && $info->to != '') ? $query->whereBetween('awarded_year',[$info->from,$info->to]) : '';
        })
        ->orderBy('awarded_year','DESC')->get();

        $scholars = ReportResource::collection($data);
        $array = [
            'scholars' => $scholars,
            'agency' => $agency
        ];

        if($info->type == 'scholars'){
            $pdf = \PDF::loadView('prints.scholars',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Scholars.pdf');
        }else if($info->type == 'graduates'){
            $pdf = \PDF::loadView('prints.graduates',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Graduate Scholars.pdf');
        }else{
            $programs = ListProgram::where('is_sub',1)->get();
            $awards = ListDropdown::where('classification','Award')->get();
            $array['awards'] = $awards; $total_no = 0; $total_awardee = 0; 
            $totals = [];
            foreach($programs as $index=>$program){
                $data = Scholar::where('is_completed',1)->where('program_id',$program->id)->count();
                $list = [];

                foreach($awards as $index2=>$award){
                    $count = Scholar::where('is_completed',1)->where('program_id',$program->id)
                    ->whereHas('education',function ($query) use ($award) {
                        $query->where('award_id',$award->id);
                    })->count();
                    array_push($list,$count);
                }

                $array2[] = [
                    'name' => $program->name,
                    'count' => $data,
                    'list' => $list,
                    'total' => array_sum($list)
                ];
                $total_no += $data;
                $total_awardee += array_sum($list);
            }

            foreach($awards as $index2=>$award){ 
                $tot = 0;
                foreach($programs as $index=>$program){
                    $tot += $array2[$index]['list'][$index2];
                }
                array_push($totals,$tot);
            }

            $array2[] = [
                'name' => 'Total',
                'count' => $total_no,
                'list' => $totals,
                'total' => $total_awardee
            ];
            $array['scholars'] = $array2;

            $pdf = \PDF::loadView('prints.awardees',$array)->setPaper('a4', 'landscape');
            return $pdf->download('List of Graduate Scholars with Awards.pdf');
        }
    }

    public static function statistics($request){
        $array = [
            Scholar::whereHas('status',function ($query) {
                $query->where('type','ongoing');
            })->count(),
            Scholar::whereHas('status',function ($query) {
                $query->where('name','Graduated');
            })->count(),
            Scholar::count()
        ];
        return $array;
    }

    public static function active(){
        $data = EnrolledList::whereHas('semester',function ($query){
            $query->where('is_active',1);
        })->pluck('scholar_id');
        return $data;
    }

    public function course($request){
        $data = ScholarEducation::where('scholar_id',$request->id)->first();

        $pros = SchoolCourseProspectus::where('school_course_id',$request->subcourse_id)->first();
        $information = [
            'prospectus' => json_decode($pros->subjects)
        ];
        $data->subcourse_id = $request->subcourse_id;
        $data->information = json_encode($information);
        if($data->save()){
            $data =  Scholar::
            with('addresses.region','addresses.province','addresses.municipality','addresses.barangay')
            ->with('profile')
            ->with('program:id,name','subprogram:id,name','category:id,name','status:id,name,type,color,others')
            ->with('education.school.school','education.course','education.level')
            ->where('id',$request->id)
            ->first();
            return new SearchResource($data);
        }   
    }
    
}