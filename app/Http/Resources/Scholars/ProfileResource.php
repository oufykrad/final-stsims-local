<?php

namespace App\Http\Resources\Scholars;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        $l = ucwords(strtolower($this->lastname)).', ';
        $f = ucwords(strtolower($this->firstname)).' ';
        $s = ($this->suffix) ? ucwords(strtolower($this->suffix)).' ' : '';
        $m = ($this->middlename) ? ucwords(strtolower($this->middlename)) : '';
        return [
            'id' => $this->id,
            'email' => $this->email,
            'avatar' => ($this->user) ? $this->user->avatar : 'avatar.jpg',
            'name' => $l.$f.$s.$m,
            'firstname' => ucwords(strtolower($this->firstname)),
            'middlename' => ucwords(strtolower($this->middlename)),
            'lastname' =>ucwords(strtolower( $this->lastname)),
            'suffix' => ($this->suffix == null) ? '' : $this->suffix,
            'sex' => $this->sex,
            'birthday' => ($this->birthday == null) ? '' : $this->birthday,
            'contact_no' => ($this->contact_no == null) ? 'n/a' : $this->contact_no
        ];
    }
}
