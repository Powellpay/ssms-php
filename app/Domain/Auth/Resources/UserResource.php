<?php

namespace App\Domain\Auth\Resources;

use App\Domain\Auth\Services\ModuleAccessService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $moduleService = app(ModuleAccessService::class);

        return [
            'id' => $this->id,
            'role_id' => $this->role_id,
            'username' => $this->username,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'email_verified_at' => $this->email_verified_at,
            'last_login' => $this->last_login,
            'is_school_admin' => $moduleService->isSchoolAdmin($this->resource),
            'modules' => $moduleService->getAllowedModules($this->resource),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
