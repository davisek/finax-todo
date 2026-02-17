<?php

namespace Modules\Todo\Http\Requests;

use Spatie\LaravelData\Data;

class UpdateTodoRequest extends Data
{
    public function __construct(
        public string $title,
        public ?string $description,
        public bool $completed,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'completed' => ['required', 'boolean'],
        ];
    }
}
