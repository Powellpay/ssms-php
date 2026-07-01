<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimetableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'stream_id' => $this->stream_id,
            'subject_id' => $this->subject_id,
            'staff_id' => $this->staff_id,
            'academic_year_id' => $this->academic_year_id,
            'day_of_week' => $this->day_of_week,
            'period_no' => $this->period_no,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
