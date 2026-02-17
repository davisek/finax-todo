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
use OpenApi\Attributes as OA;
use Spatie\LaravelData\PaginatedDataCollection;

#[OA\Info(version: '1.0.0', title: 'Finax Todo API')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer')]
class TodoController extends Controller
{
    public function __construct(
        private readonly ITodoService $todoService,
    ) {}

    #[OA\Get(
        path: '/api/v1/todos',
        summary: 'Get all todos',
        security: [['bearerAuth' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['completed', 'pending'])),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 10)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(
                                property: 'data',
                                type: 'array',
                                items: new OA\Items(ref: '#/components/schemas/TodoSimpleResource')
                            ),
                            new OA\Property(
                                property: 'pagination',
                                properties: [
                                    new OA\Property(property: 'meta', properties: [
                                        new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                        new OA\Property(property: 'last_page', type: 'integer', example: 3),
                                        new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                        new OA\Property(property: 'total', type: 'integer', example: 25),
                                    ], type: 'object'),
                                ],
                                type: 'object'
                            ),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/v1/todos/{id}',
        summary: 'Get single todo',
        security: [['bearerAuth' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TodoResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(Todo $todo)
    {
        return AppResponse::success(
            data: TodoResource::from($todo)
        );
    }

    #[OA\Post(
        path: '/api/v1/todos',
        summary: 'Create todo',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                ]
            )
        ),
        tags: ['Todos'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TodoResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(CreateTodoRequest $request)
    {
        $todo = $this->todoService->create(Auth::user(), $request);

        return AppResponse::successToast(
            message: Lang::get('todo::success.CREATED'),
            data: TodoResource::from($todo),
            statusCode: 201
        );
    }

    #[OA\Put(
        path: '/api/v1/todos/{id}',
        summary: 'Update todo',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'completed'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', maxLength: 255),
                    new OA\Property(property: 'description', type: 'string', nullable: true),
                    new OA\Property(property: 'completed', type: 'boolean'),
                ]
            )
        ),
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TodoResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        $todo = $this->todoService->update($todo, $request);

        return AppResponse::successToast(
            message: Lang::get('todo::success.UPDATED'),
            data: TodoResource::from($todo)
        );
    }

    #[OA\Delete(
        path: '/api/v1/todos/{id}',
        summary: 'Delete todo',
        security: [['bearerAuth' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(Todo $todo)
    {
        $this->todoService->delete($todo);

        return AppResponse::successToast(
            message: Lang::get('todo::success.DELETED')
        );
    }

    #[OA\Patch(
        path: '/api/v1/todos/{id}/toggle',
        summary: 'Toggle todo completion status',
        security: [['bearerAuth' => []]],
        tags: ['Todos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TodoResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function toggle(Todo $todo)
    {
        $todo = $this->todoService->toggle($todo);

        return AppResponse::successToast(
            message: Lang::get('todo::success.TOGGLED'),
            data: TodoResource::from($todo)
        );
    }

    #[OA\Get(
        path: '/api/v1/todos/stats',
        summary: 'Get todo statistics',
        security: [['bearerAuth' => []]],
        tags: ['Todos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    allOf: [
                        new OA\Schema(ref: '#/components/schemas/AppResponse'),
                        new OA\Schema(properties: [
                            new OA\Property(property: 'data', ref: '#/components/schemas/TodoStatsResource'),
                        ], type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
        ]
    )]
    public function stats()
    {
        $stats = $this->todoService->stats(Auth::user());

        return AppResponse::success(data: $stats);
    }
}
