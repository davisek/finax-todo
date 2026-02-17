<?php

namespace Modules\Todo\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\User\Models\User;

/**
 * Class Todo
 *
 * @property int $id
 * @property int $user_id
 *
 * @property string $title
 * @property string $description
 * @property bool $completed

 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property User $user
 */
class Todo extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
