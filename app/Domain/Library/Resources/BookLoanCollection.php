<?php

namespace App\Domain\Library\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookLoanCollection extends ResourceCollection
{
    public $collects = BookLoanResource::class;

    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
