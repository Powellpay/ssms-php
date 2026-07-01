<?php

namespace App\Domain\Assessment\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'subject_id' => $this->subject_id,
            'theme_id' => $this->theme_id,
            'learning_outcome_id' => $this->learning_outcome_id,
            'assessment_type_id' => $this->assessment_type_id,
            'term_id' => $this->term_id,
            'score' => $this->score,
            'max_score' => $this->max_score,
            'grade' => $this->grade,
            'remarks' => $this->remarks,
            'date_recorded' => $this->date_recorded,
            'recorded_by' => $this->recorded_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
