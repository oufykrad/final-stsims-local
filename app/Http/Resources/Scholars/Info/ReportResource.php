<?php

namespace App\Http\Resources\Scholars\Info;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request)
    {
        $info =  json_decode($this->profile->information);
        // dd($info);
        // $this->education->courseInfo = ['name' => $info->course];
        // $this->education->schoolInfo = ['name' => $info->school];
        // $this->profile->address->info = ['info' => $info->address];
        
        return [
            'id' => $this->id,
            'spas_id' => ($this->spas_id == null) ? 'n/a' : $this->spas_id,
            'awarded_year' => $this->awarded_year,
            'program' => $this->program,
            'status' => $this->status,
            'is_completed' => $this->is_completed,
            'profile' => new ProfileResource($this->profile), 
            'user' => ($this->user != null) ? new UserResource($this->user) : null,
            'address' => new AddressResource($this->addresses[0]),
            'education' =>  new EducationResource($this->education),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
