<?php

namespace App\Http\Resources\Scholars\Benefits;

use Illuminate\Http\Resources\Json\JsonResource;

class ListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'account_no' => ($this->account_no == null) ? 'n/a' : $this->account_no,
            'name' => $this->profile->lastname.', '. $this->profile->firstname,
            'program' => $this->program->name,
            'avatar' => ($this->profile->user) ? $this->profile->user->avatar : 'avatar.jpg',
            'benefits' => $this->benefits,
            'total' => $this->benefits->sum('amount')
        ];
    }
}
