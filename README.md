# Finax Todo API

A RESTful Todo API built with Laravel 12, featuring token-based authentication, modular architecture, and full API documentation.

---

## Technologies Used

- **PHP 8.4**
- **Laravel 12**
- **PostgreSQL 16**
- **Laravel Sanctum** – token-based authentication (access + refresh token pair)
- **nwidart/laravel-modules** – modular application structure
- **spatie/laravel-data** – typed DTOs for requests and resources
- **darkaonline/l5-swagger** – OpenAPI 3.0 documentation
- **Docker + Nginx** – containerized setup

---

## Quick Start (Docker)

```bash
git clone <repo-url>
cd finax-todo
./docker/setup.sh
```

That's it. The script will:
- Copy `.env.example` to `.env`
- Build and start all containers
- Generate app key
- Run migrations and seeders
- Generate Swagger documentation

**App:** http://localhost:8000  
**Swagger UI:** http://localhost:8000/api/documentation

**Test credentials:**
| Email | Password |
|---|---|
| test@gmail.com | 12345678 |
| test+2@gmail.com | 12345678 |

---

## Manual Setup (without Docker)

**Requirements:** PHP 8.4, Composer, PostgreSQL

```bash
git clone <repo-url>
cd finax-todo

composer install

cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials:
```env
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=finax_todo
DB_USERNAME=your_user
DB_PASSWORD=your_password
```

```bash
php artisan migrate --seed
php artisan l5-swagger:generate
php artisan serve
```

---

## API Documentation

Interactive Swagger UI is available at `/api/documentation` after running `php artisan l5-swagger:generate`.

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer <access_token>
```

The refresh token is stored automatically as an `httpOnly` cookie.

---

### Auth Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| POST | `/auth/register` | Register new user | ❌ |
| POST | `/auth/login` | Login user | ❌ |
| POST | `/auth/logout` | Logout current session | ✅ |
| POST | `/auth/refresh` | Refresh access token (via cookie) | ❌ |
| POST | `/auth/revoke` | Revoke all tokens | ✅ |
| GET | `/auth/check` | Validate current token | ✅ |
| GET | `/auth/me` | Get current user | ✅ |

### Todo Endpoints

| Method | Endpoint | Description | Auth |
|--------|----------|-------------|------|
| GET | `/todos` | Get all todos (paginated) | ✅ |
| POST | `/todos` | Create todo | ✅ |
| GET | `/todos/{id}` | Get single todo | ✅ |
| PUT | `/todos/{id}` | Update todo | ✅ |
| DELETE | `/todos/{id}` | Delete todo | ✅ |
| PATCH | `/todos/{id}/toggle` | Toggle completion status | ✅ |
| GET | `/todos/stats` | Get statistics | ✅ |

### Query Parameters (GET /todos)

| Parameter | Type | Description |
|-----------|------|-------------|
| `status` | `completed` \| `pending` | Filter by status |
| `search` | string | Search in title and description |
| `per_page` | integer (1–100) | Items per page (default: 10) |

### Example Requests

**Register**
```json
POST /api/v1/auth/register
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "Password1!",
  "password_confirmation": "Password1!"
}
```

**Create Todo**
```json
POST /api/v1/todos
Authorization: Bearer <token>

{
  "title": "Buy groceries",
  "description": "Milk, eggs, bread"
}
```

**Response Format**
```json
{
  "type": "success",
  "toast": true,
  "message": "Todo created successfully.",
  "data": {
    "id": 1,
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "completed": false,
    "created_at": "2026-02-17 12:00:00"
  }
}
```

---

## Project Structure

```
finax-todo/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── setup.sh
├── Dockerfile
├── docker-compose.yml
├── Modules/
│   ├── General/                    # Shared utilities
│   │   ├── Classes/Enums/          # ResponseType enum
│   │   └── Http/Resources/         # AppResponse class
│   ├── User/                       # Auth & user management
│   │   ├── Http/
│   │   │   ├── Controllers/        # Login, Register, Logout, Refresh, Auth
│   │   │   ├── Requests/           # LoginRequest, RegisterRequest
│   │   │   └── Resources/          # AuthResource
│   │   ├── Models/User.php
│   │   ├── Services/               # AuthService, UserService
│   │   ├── OpenApi/Schemas/        # Swagger schema definitions
│   │   ├── Database/
│   │   │   ├── Migrations/
│   │   │   └── Seeders/
│   │   └── resources/lang/                   # en, sk, cs translations
│   └── Todo/                       # Todo CRUD
│       ├── Http/
│       │   ├── Controllers/        # TodoController
│       │   ├── Requests/           # CreateTodoRequest, UpdateTodoRequest, TodoIndexRequest
│       │   ├── Middlewares/        # TodoBelongsToUser
│       │   └── Resources/          # TodoResource, TodoSimpleResource, TodoStatsResource
│       ├── Models/Todo.php
│       ├── Services/               # TodoService
│       ├── Classes/Enums/          # StatusFilter
│       ├── OpenApi/Schemas/        # Swagger schema definitions
│       ├── Database/
│       │   ├── Migrations/
│       │   └── Seeders/
│       └── resources/lang/                   # en, sk, cs translations
└── app/
    └── Providers/
        └── AppServiceProvider.php  # Rate limiting configuration
```

---

## Design Decisions & Trade-offs

**Modular architecture (nwidart/laravel-modules)**  
The application is split into `General`, `User`, and `Todo` modules. This keeps concerns separated and makes the codebase easier to navigate and extend. The trade-off is slightly more boilerplate compared to a flat Laravel structure.

**Access + Refresh token pair via Sanctum**  
Instead of a single long-lived token, the app issues a short-lived access token (15 min) and a long-lived refresh token (7 days) stored in an `httpOnly` cookie. This reduces the impact of a leaked access token. The `session:uuid` shared ability ties both tokens to the same session, enabling precise per-session logout.

**Spatie Laravel Data for requests and resources**  
Using typed DTOs instead of plain `FormRequest` and `Resource` classes gives better type safety and autocompletion. The trade-off is an additional dependency and a slightly different API compared to standard Laravel.

**Soft deletes on todos**  
Todos are soft-deleted rather than hard-deleted. This means deleted todos can be recovered if needed, with no extra implementation cost.

**Middleware for todo ownership**  
`TodoBelongsToUser` middleware handles authorization at the route level. This keeps controllers clean and makes the authorization boundary explicit. A Policy was considered but middleware was chosen for simplicity in this context.

**Granular rate limiting**  
Each auth endpoint has its own rate limiter (`auth-login`, `auth-register`, etc.) with higher limits in debug mode. This prevents brute force attacks while not getting in the way during development.

**Multilingual support (en, sk, cs)**  
All user-facing messages are stored in language files for English, Slovak, and Czech. The `AppResponse` class always returns a consistent JSON envelope regardless of language.

---

## Future Improvements

- **API tests** – Feature tests for all endpoints using Pest or PHPUnit
- **Tags / categories** for todos
- **Due dates** with overdue filtering
- **Priority levels** (low, medium, high)
- **Shared todos** – allow users to share todos with others
- **Email verification** on registration
- **Password reset** flow
- **Redis** for caching and rate limiting in production instead of file/database drivers
- **CI/CD pipeline** – GitHub Actions for running tests and linting on push
