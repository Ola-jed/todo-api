<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Database\Factories\TaskFactory;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property bool $has_steps
 * @property string $description
 * @property string $date_limit
 * @property bool $is_finished
 * @property int $priority
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static TaskFactory factory( ...$parameters )
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task query()
 * @method static Builder|Task whereCreatedAt( $value )
 * @method static Builder|Task whereDateLimit( $value )
 * @method static Builder|Task whereDescription( $value )
 * @method static Builder|Task whereHasSteps( $value )
 * @method static Builder|Task whereId( $value )
 * @method static Builder|Task whereIsFinished( $value )
 * @method static Builder|Task wherePriority( $value )
 * @method static Builder|Task whereSlug( $value )
 * @method static Builder|Task whereTitle( $value )
 * @method static Builder|Task whereUpdatedAt( $value )
 * @method static Builder|Task whereUserId( $value )
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Step[] $steps
 * @property-read int|null $steps_count
 */
class Task extends Model
{
    use HasFactory;

    /**
     * Get a task with its slug
     * @param string $slug
     * @return Task
     */
    public static function getBySlug(string $slug): Task
    {
        return Task::whereSlug($slug)
            ->firstOrFail();
    }

    /**
     * Get all the steps of this task
     * @return HasMany
     */
    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    /**
     * Create a new task from data
     * The same user should not have two tasks with the same slug
     * @param array $data
     * @param int $userId
     * @return Task
     * @throws Exception
     */
    public static function createFromData(array $data, int $userId): Task
    {
        $slug = Str::slug($data['title']);
        $sameTaskExists = Task::whereSlug($slug)
            ->where('user_id', $userId)
            ->exists();
        if($sameTaskExists)
        {
            Log::warning('Task existing');
            throw new Exception();
        }
        $task = Task::create([
            'title'       => $data['title'],
            'slug'        => $slug,
            'has_steps'   => $data['has_steps'],
            'description' => $data['description'],
            'date_limit'  => $data['date_limit'],
            'priority'    => $data['priority'],
            'user_id'     => $userId
        ]);
        if(is_null($task))
        {
            Log::warning('Task not created');
            throw new Exception();
        }
        return $task;
    }

    /**
     * Update a task with the new data given
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateWithData(array $data): bool
    {
        $newSlug = Str::slug($data['title']);
        $sameTaskExists = Task::whereSlug($newSlug)
            ->where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->exists();
        if($sameTaskExists)
        {
            return false;
        }
        return $this->update([
            'title'       => $data['title'],
            'slug'        => $newSlug,
            'description' => $data['description'],
            'priority'    => $data['priority'],
            'has_steps'   => $data['has_steps'],
            'date_limit'  => $data['date_limit']
        ]);
    }

    protected $fillable = [
        'title',
        'slug',
        'has_steps',
        'description',
        'date_limit',
        'is_finished',
        'priority',
        'user_id'
    ];
}
