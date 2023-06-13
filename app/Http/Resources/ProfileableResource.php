<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileableResource extends JsonResource
{
    public function toArray($request)
    {
        switch($this->type){
            case 'App\Models\SchoolCampus':
                $name = ucwords(strtolower($this->school->name));
            break;
            case 'App\Models\LocationRegion':
                $name = $this->region;
            break;
            case 'App\Models\LocationProvince':
                $name = $this->name;
            break;
        }
        return [
            'name' => $name
        ];
    }
}
