<?php

namespace App\Domain\Curriculum\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SubjectTeacherCollection extends ResourceCollection
{
    public $collects = SubjectTeacherResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
