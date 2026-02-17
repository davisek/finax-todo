<?php

namespace Modules\User\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 *
 * @property int $id
 *
 * @property string $name
 * @property string $email
 * @property string $password

 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property Collection|Todo[] $todos
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'last_login' => 'datetime',
        'last_seen_at' => 'datetime',
        'password' => 'hashed'
    ];

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class, 'user_id');
    }
}
