<?php

namespace Modules\Todo\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Todo\Classes\Enums\StatusFilter;
use Spatie\LaravelData\Data;

class TodoIndexRequest extends Data
{
    public function __construct(
        public ?int $per_page = 10,
        public ?StatusFilter $status = null,
        public ?string $search = null,
    ) {}

    public static function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'string', Rule::in(StatusFilter::values())],
            'search' => ['nullable', 'string', 'max:255'],
        ];
    }
}
