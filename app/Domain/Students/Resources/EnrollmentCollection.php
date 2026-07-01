<?php

namespace App\Domain\Students\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class EnrollmentCollection extends ResourceCollection
{
    public $collects = EnrollmentResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
