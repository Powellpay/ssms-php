<?php

namespace App\Domain\Academic\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StreamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('stream');

        return [
            'class_level_id' => 'required|exists:class_levels,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'stream_name' => 'required|string|max:30|unique:streams,stream_name,' . $id . ',id,class_level_id,' . $this->class_level_id . ',academic_year_id,' . $this->academic_year_id,
            'class_teacher_id' => 'nullable|exists:staff,id',
        ];
    }
}
