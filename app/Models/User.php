<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * Class User
 * Users of the app
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static UserFactory factory( ...$parameters )
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt( $value )
 * @method static Builder|User whereEmail( $value )
 * @method static Builder|User whereId( $value )
 * @method static Builder|User whereName( $value )
 * @method static Builder|User wherePassword( $value )
 * @method static Builder|User whereRememberToken( $value )
 * @method static Builder|User whereUpdatedAt( $value )
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Create a new user with validated data
     * @param array $data
     * @return User
     * @throws Exception
     */
    public static function createFromData(array $data): User
    {
        $usr = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password1'])
        ]);
        if(is_null($usr))
        {
            throw new Exception();
        }
        return $usr;
    }

    /**
     * Get all the tasks created by the user
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Find a task created by the user with it's slug
     * @param string $slug
     * @return Model|HasMany
     */
    public function getTaskBySlug(string $slug): Model|HasMany
    {
        return $this->tasks()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Is tis user the creator of the step ?
     * @param int $stepId
     * @return bool
     */
    public function isStepAuthor(int $stepId): bool
    {
        $step = Step::find($stepId);
        if(is_null($step))
        {
            return false;
        }
        return $this->tasks()
            ->where('id', $step->task_id)
            ->exists();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    /**
     * No timestamps are needed
     * @var bool
     */
    public $timestamps = false;
}
