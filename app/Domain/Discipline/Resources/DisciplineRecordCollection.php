<?php

namespace App\Domain\Discipline\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DisciplineRecordCollection extends ResourceCollection
{
    public $collects = DisciplineRecordResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
