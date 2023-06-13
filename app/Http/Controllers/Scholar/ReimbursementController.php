<?php

namespace App\Http\Controllers\Scholar;

use App\Models\Scholar;
use App\Models\ScholarBenefit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\UploadTrait;
use App\Http\Requests\ReimbursementRequest;
use App\Http\Resources\Scholars\Benefits\ReimbursementResource;
use App\Http\Resources\Scholars\SearchResource;

class ReimbursementController extends Controller
{
    use UploadTrait;

    public function index(Request $request){
        $keyword = $request->keyword;
        if($keyword != ''){
            $data = Scholar::with('profile','status','program')->with('education.school.semesters.semester')
            ->when($request->keyword, function ($query, $keyword) {
                $query->whereHas('profile',function ($query) use ($keyword) {
                    $query->where(\DB::raw('concat(firstname," ",lastname)'), 'LIKE', '%'.$keyword.'%')
                    ->orWhere(\DB::raw('concat(lastname," ",firstname)'), 'LIKE', '%'.$keyword.'%')
                    ->orWhere('spas_id','LIKE','%'.$keyword.'%');
                });
            })
            ->take(5)->get();
            return SearchResource::collection($data);
        }
    }

    public function store(ReimbursementRequest $request){
        $data = \DB::transaction(function () use ($request){
            $attachments = [
                $this->reimbursement($request)
            ];
            $data = ScholarBenefit::create(array_merge($request->all(),['attachment' => json_encode($attachments), 'release_type' => 'Full', 'month' => now(), 'status_id' => 11]));
            return new ReimbursementResource($data);
        });
        
        return back()->with([
            'message' => 'Reimbursement successful. Thanks',
            'data' =>  $data,
            'type' => 'bxs-check-circle'
        ]); 
    }
}
