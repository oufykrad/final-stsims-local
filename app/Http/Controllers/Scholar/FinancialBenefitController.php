<?php

namespace App\Http\Controllers\Scholar;

use App\Models\User;
use App\Models\Scholar;
use App\Models\Release;
use App\Models\ListPrivilege;
use App\Models\ScholarBenefit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\NameResource;
use App\Http\Requests\ReleaseRequest;
use App\Http\Traits\UploadTrait;
use App\Http\Resources\Scholars\Benefits\ListResource;
use App\Http\Resources\Scholars\Benefits\ReleaseResource;

class FinancialBenefitController extends Controller
{
    use UploadTrait;

    public function index(Request $request){
        $type = $request->type;
        switch($type){
            case 'lists':
                return $this->lists($request);
            break;
            default : 
            return inertia('Modules/FinancialBenefits/Index');
        }
    }

    public function store(ReleaseRequest $request){
        if($request->type == 'Completed'){
            $data = \DB::transaction(function () use ($request){
                $attachments = $this->release($request);
                $benefit = ScholarBenefit::where('release_id',$request->id)->update(['status_id' => 13]);
                $data = Release::where('id',$request->id)->update(['status_id' => 13, 'attachment' => json_encode($attachments)]);
                $data = Release::where('id',$request->id)->first();
                return new ReleaseResource($data);
            });
            
            return back()->with([
                'message' => 'Mark as completed. Thanks',
                'data' =>  $data,
                'type' => 'bxs-check-circle'
            ]); 
        }else{
            $data = \DB::transaction(function () use ($request){
                $attachment = [];
                $count = Release::whereYear('created_at',now())->count();
                $data = Release::create(
                    array_merge($request->all(),[
                        'attachment' => json_encode($attachment),
                        'added_by' => \Auth::user()->id,
                        'batch' => str_pad(($count+1), 5, '0', STR_PAD_LEFT),
                        'status_id' => 11
                    ])
                );
                foreach($request->lists as $list){
                    foreach($list['benefits'] as $benefit){
                        $benefit = ScholarBenefit::where('id',$benefit['id'])->first();
                        $benefit->status_id = 12;
                        $benefit->release_id = $data->id;
                        $benefit->save();
                    }
                }
                return $data;
            });

            return back()->with([
                'message' => 'Released was successfull. Thanks',
                'data' =>  $data,
                'type' => 'bxs-check-circle'
            ]); 
        }
    }

    public function create(Request $request){
        switch($request->type){
            case 'generate':
                return $this->generate();
            break;
            case 'benefits':
                return $this->benefits($request->info);
            break;
        }
    }

    public function lists($request){
        $month = date_parse($request->month)['month'];
        $year = $request->year;
        
        $data = Release::orderBy('created_at','DESC')
        ->when($month, function ($query, $month) {
            $query->whereMonth('created_at',$month);
        })
        ->when($year, function ($query, $year) {
            $query->whereYear('created_at',$year);
        })
        ->when($request->keyword, function ($query, $keyword) {
            $query->where('batch','LIKE','%'.$keyword.'%');
        })
        ->paginate($request->count)
        ->withQueryString();

        return ReleaseResource::collection($data);
    }

    public function generate(){
        // $date = date("Y").'-'.date("m").'-1';
        $date = now();
        $pending = ScholarBenefit::where('status_id',11)->where('month','<=',$date)->groupBy('scholar_id')->pluck('scholar_id');
        $scholars = Scholar::with('profile')->whereIn('id',$pending)->get();
        $data = [
            'pending' => $pending,
            'scholars' => NameResource::collection($scholars),
            'month' => date('F', mktime(0, 0, 0, date("m"), 10)),
            'count' => Release::whereYear('created_at', '=', date("Y"))->whereMonth('created_at', '=', date("m"))->count()
        ];
        return $data;
    }

    public function benefits($info){
        $scholars = (!empty(json_decode($info))) ? json_decode($info) : NULL;
        $month = now();
        //date("Y").'-'.date("m").'-1'
        $data = Scholar::with('profile',)->with('benefits.benefit')->with('program')
        ->withWhereHas('benefits', function ($query) use ($month) {
            $query->where('status_id',11)->where('month','<=',$month);
        })
        ->whereIn('id',$scholars)
        ->get();

        return ListResource::collection($data);
    }

    public function edit($id){
        $data = Release::where('id',$id)->first();
        $user = User::with('profile')->where('role','Scholarship Coordinator')->where('is_active',1)->first();
        $lists = ScholarBenefit::where('release_id',$id)->pluck('benefit_id');
        $lists = ListPrivilege::whereIn('id',$lists)->pluck('name');
        $array = [
            'benefits' => new ReleaseResource($data),
            'user' => $user,
            'lists' => $lists
        ];

        $pdf = \PDF::loadView('prints2.fb',$array)->setPaper('a4', 'portrait');
        return $pdf->download('FinancialBenefit.pdf');
    }
}
