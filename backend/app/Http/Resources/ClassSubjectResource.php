<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_level_id' => $this->class_level_id,
            'subject_id' => $this->subject_id,
            'is_compulsory' => $this->is_compulsory,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
