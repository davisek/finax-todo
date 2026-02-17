<?php

namespace Modules\Todo\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Todo\Classes\Enums\StatusFilter;
use Modules\Todo\Http\Requests\CreateTodoRequest;
use Modules\Todo\Http\Requests\TodoIndexRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Http\Resources\TodoStatsResource;
use Modules\Todo\Models\Todo;
use Modules\Todo\Services\Interfaces\ITodoService;
use Modules\User\Models\User;

class TodoService implements ITodoService
{
    public function getAll(User $user, TodoIndexRequest $query): LengthAwarePaginator
    {
        $q = Todo::where('user_id', $user->id);

        if ($query->status === StatusFilter::COMPLETED) {
            $q->where('completed', true);
        } elseif ($query->status === StatusFilter::PENDING) {
            $q->where('completed', false);
        }

        if ($query->search) {
            $q->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query->search}%")
                    ->orWhere('description', 'like', "%{$query->search}%");
            });
        }

        return $q->latest()->paginate($query->per_page);
    }

    public function create(User $user, CreateTodoRequest $request): Todo
    {
        return Todo::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'completed' => false,
        ]);
    }

    public function update(Todo $todo, UpdateTodoRequest $request): Todo
    {
        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'completed' => $request->completed,
        ]);

        return $todo->refresh();
    }

    public function delete(Todo $todo): void
    {
        $todo->delete();
    }

    public function toggle(Todo $todo): Todo
    {
        $todo->update(['completed' => !$todo->completed]);

        return $todo->refresh();
    }

    public function stats(User $user): TodoStatsResource
    {
        $total = Todo::where('user_id', $user->id)->count();
        $completed = Todo::where('user_id', $user->id)->where('completed', true)->count();

        return TodoStatsResource::from([
            'total' => $total,
            'completed' => $completed,
            'pending' => $total - $completed,
        ]);
    }
}
