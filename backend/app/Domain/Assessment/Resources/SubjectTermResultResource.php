<?php

namespace App\Domain\Assessment\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectTermResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'subject_id' => $this->subject_id,
            'term_id' => $this->term_id,
            'ca_score' => $this->ca_score,
            'eot_score' => $this->eot_score,
            'final_score' => $this->final_score,
            'final_grade' => $this->final_grade,
            'subject_teacher_comment' => $this->subject_teacher_comment,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
