<?php

namespace App\Domain\Staff\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'staff_no' => $this->staff_no,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'dob' => $this->dob,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'designation' => $this->designation,
            'date_joined' => $this->date_joined,
            'photo' => $this->photo,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->whenLoaded('user', fn() => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'username' => $this->user->username,
                'name' => $this->user->name,
                'status' => $this->user->status,
                'modules' => $this->user->modules ?? [],
                'role' => $this->user->relationLoaded('role') && $this->user->role
                    ? [
                        'id' => $this->user->role->id,
                        'role_name' => $this->user->role->role_name,
                      ]
                    : null,
            ]),
        ];
    }
}
