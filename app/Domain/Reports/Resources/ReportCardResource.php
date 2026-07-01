<?php

namespace App\Domain\Reports\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportCardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'term_id' => $this->term_id,
            'stream_id' => $this->stream_id,
            'days_present' => $this->days_present,
            'days_absent' => $this->days_absent,
            'class_teacher_comment' => $this->class_teacher_comment,
            'head_teacher_comment' => $this->head_teacher_comment,
            'next_term_begins' => $this->next_term_begins,
            'date_issued' => $this->date_issued,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
