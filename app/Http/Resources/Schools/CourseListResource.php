<?php

namespace App\Http\Resources\Schools;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->course->id,
            'name' => $this->course->name,
        ];
    }
}
