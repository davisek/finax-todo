<?php

namespace Modules\Todo\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Modules\General\Http\Resources\AppResponse;
use Modules\Todo\Http\Requests\CreateTodoRequest;
use Modules\Todo\Http\Requests\TodoIndexRequest;
use Modules\Todo\Http\Requests\UpdateTodoRequest;
use Modules\Todo\Http\Resources\TodoResource;
use Modules\Todo\Http\Resources\TodoSimpleResource;
use Modules\Todo\Models\Todo;
use Modules\Todo\Services\Interfaces\ITodoService;
use Spatie\LaravelData\PaginatedDataCollection;

class TodoController extends Controller
{
    public function __construct(
        private readonly ITodoService $todoService,
    ) {}

    public function index(Request $request)
    {
        $query = TodoIndexRequest::from($request->query());

        $todos = $this->todoService->getAll(
            user: Auth::user(),
            query: $query,
        );

        return AppResponse::success(
            data: new PaginatedDataCollection(TodoSimpleResource::class, $todos)
        );
    }

    public function show(Todo $todo)
    {
        return AppResponse::success(
            data: TodoResource::from($todo)
        );
    }

    public function store(CreateTodoRequest $request)
    {
        $todo = $this->todoService->create(Auth::user(), $request);

        return AppResponse::successToast(
            message: Lang::get('todo::success.CREATED'),
            data: TodoResource::from($todo),
            statusCode: 201
        );
    }

    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo = $this->todoService->update($todo, $request);

        return AppResponse::successToast(
            message: Lang::get('todo::success.UPDATED'),
            data: TodoResource::from($todo)
        );
    }

    public function destroy(Todo $todo)
    {
        $this->todoService->delete($todo);

        return AppResponse::successToast(
            message: Lang::get('todo::success.DELETED')
        );
    }

    public function toggle(Todo $todo)
    {
        $todo = $this->todoService->toggle($todo);

        return AppResponse::successToast(
            message: Lang::get('todo::success.TOGGLED'),
            data: TodoResource::from($todo)
        );
    }

    public function stats()
    {
        $stats = $this->todoService->stats(Auth::user());

        return AppResponse::success(data: $stats);
    }
}
