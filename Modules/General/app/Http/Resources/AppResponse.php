<?php

namespace Modules\General\Http\Resources;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Modules\General\Classes\Enums\ResponseType;
use Spatie\LaravelData\PaginatedDataCollection;

class AppResponse implements Responsable
{
    public static $wrap = 'data';
    protected ResponseType $type;
    protected bool $isToast;
    protected ?string $message;
    protected mixed $data;
    protected ?array $pagination;
    protected ?array $errors;
    protected ?array $auth;
    protected int $statusCode;
    protected array $cookies = [];

    /**
     * AppResponse constructor.
     */
    public function __construct(
        ResponseType $type,
        bool $isToast = false,
        ?string $message = null,
        mixed $data = null,
        ?array $pagination = null,
        ?array $errors = null,
        ?array $auth = null,
        int $statusCode = 200
    ) {
        $this->type = $type;
        $this->isToast = $isToast;
        $this->message = $message;
        $this->data = $data;
        $this->pagination = $pagination;
        $this->errors = $errors;
        $this->auth = $auth;
        $this->statusCode = $statusCode;
    }

    /**
     * Build the JSON payload and return a JsonResponse.
     */
    public function toResponse($request): JsonResponse
    {
        $payload = array_filter([
            'type' => $this->type->value,
            'toast' => $this->isToast,
            'message' => $this->message,
            'data' => $this->data,
            'pagination' => $this->pagination,
            'errors' => $this->errors,
            'auth' => $this->auth,
        ], fn($value) => $value !== null);

        if ($this->data instanceof PaginatedDataCollection) {
            $paginated = $this->data;
            $arr = $paginated->toArray($request);

            $payload['data'] = $arr['data'];
            $payload['pagination'] = [
                'meta' => $arr['meta'] ?? null,
                'links' => $arr['links'] ?? null,
            ];
        } else {
            $payload['data'] = $this->data;
            if ($this->pagination !== null) {
                $payload['pagination'] = $this->pagination;
            }
        }

        if ($this->errors !== null)
            $payload['errors'] = $this->errors;
        if ($this->auth !== null)
            $payload['auth'] = $this->auth;

        $response = response()->json($payload, $this->statusCode);

        foreach ($this->cookies as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    public function addCookie($cookie): self
    {
        $this->cookies[] = $cookie;
        return $this;
    }

    public function addCookies(array $cookies): self
    {
        foreach ($cookies as $cookie) {
            $this->cookies[] = $cookie;
        }
        return $this;
    }

    //====== Static constructors returning AppResponse ======

    public static function success(
        ?string $message = null,
        mixed $data = null,
        ?array $pagination = null,
        ?array $auth = null,
        int $statusCode = 200
    ): self {
        return new self(
            type: ResponseType::SUCCESS,
            isToast: false,
            message: $message ?? Lang::get('general::response.SUCCESS_RESPONSE'),
            data: $data,
            pagination: $pagination,
            errors: null,
            auth: $auth,
            statusCode: $statusCode
        );
    }

    public static function successToast(
        ?string $message = null,
        mixed $data = null,
        ?array $pagination = null,
        ?array $auth = null,
        int $statusCode = 200
    ): self {
        return new self(
            type: ResponseType::SUCCESS,
            isToast: true,
            message: $message ?? Lang::get('general::response.SUCCESS_RESPONSE'),
            data: $data,
            pagination: $pagination,
            errors: null,
            auth: $auth,
            statusCode: $statusCode
        );
    }

    public static function error(
        ?string $message = null,
        ?array $errors = null,
        mixed $data = null,
        int $statusCode = 500
    ): self {
        return new self(
            type: ResponseType::ERROR,
            isToast: false,
            message: $message ?? Lang::get('general::response.ERROR_RESPONSE'),
            data: $data,
            pagination: null,
            errors: $errors,
            auth: null,
            statusCode: $statusCode
        );
    }

    public static function errorToast(
        ?string $message = null,
        ?array $errors = null,
        mixed $data = null,
        int $statusCode = 500
    ): self {
        return new self(
            type: ResponseType::ERROR,
            isToast: true,
            message: $message ?? Lang::get('general::response.ERROR_RESPONSE'),
            data: $data,
            pagination: null,
            errors: $errors,
            auth: null,
            statusCode: $statusCode
        );
    }

    public static function warning(
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 400
    ): self {
        return new self(
            type: ResponseType::WARNING,
            isToast: false,
            message: $message ?? Lang::get('general::response.WARNING_RESPONSE'),
            data: $data,
            pagination: null,
            errors: null,
            auth: null,
            statusCode: $statusCode
        );
    }

    public static function warningToast(
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 400
    ): self {
        return new self(
            type: ResponseType::WARNING,
            isToast: true,
            message: $message ?? Lang::get('general::response.WARNING_RESPONSE'),
            data: $data,
            pagination: null,
            errors: null,
            auth: null,
            statusCode: $statusCode
        );
    }

    public static function info(
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 200
    ): self {
        return new self(
            type: ResponseType::INFO,
            isToast: false,
            message: $message ?? Lang::get('general::response.INFO_RESPONSE'),
            data: $data,
            pagination: null,
            errors: null,
            auth: null,
            statusCode: $statusCode
        );
    }

    public static function infoToast(
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 200
    ): self {
        return new self(
            type: ResponseType::INFO,
            isToast: true,
            message: $message ?? Lang::get('general::response.INFO_RESPONSE'),
            data: $data,
            pagination: null,
            errors: null,
            auth: null,
            statusCode: $statusCode
        );
    }
}
