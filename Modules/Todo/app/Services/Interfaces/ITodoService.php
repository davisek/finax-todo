<?php

namespace Modules\Todo\Services\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Todo\Http\Requests\CreateTodoRequest;
use Modules\Todo\Http\Requests\TodoIndexRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Http\Resources\TodoStatsResource;
use Modules\Todo\Models\Todo;
use Modules\User\Models\User;

interface ITodoService
{
    public function getAll(User $user, TodoIndexRequest $query): LengthAwarePaginator;

    public function create(User $user, CreateTodoRequest $request): Todo;

    public function update(Todo $todo, UpdateTodoRequest $request): Todo;

    public function delete(Todo $todo): void;

    public function toggle(Todo $todo): Todo;

    public function stats(User $user): TodoStatsResource;
}
