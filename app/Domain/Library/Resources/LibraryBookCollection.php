<?php

namespace App\Domain\Library\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LibraryBookCollection extends ResourceCollection
{
    public $collects = LibraryBookResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
