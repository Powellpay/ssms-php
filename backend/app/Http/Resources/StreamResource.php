<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StreamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'class_level_id' => $this->class_level_id,
            'academic_year_id' => $this->academic_year_id,
            'stream_name' => $this->stream_name,
            'class_teacher_id' => $this->class_teacher_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
