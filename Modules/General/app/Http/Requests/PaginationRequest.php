<?php

namespace Modules\General\Http\Requests;

use Spatie\LaravelData\Data;

class PaginationRequest extends Data
{
    public function __construct(
        public ?int $per_page = 15
    ) {
    }

    public static function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
