<?php

namespace App\Domain\Curriculum\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GenericSkillCollection extends ResourceCollection
{
    public $collects = GenericSkillResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
