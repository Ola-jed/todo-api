<?php

namespace App\Models;

use Barryvdh\LaravelIdeHelper\Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Step
 *
 * @property int $id
 * @property string $title
 * @property int $priority
 * @property bool $is_finished
 * @property int $task_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Step newModelQuery()
 * @method static Builder|Step newQuery()
 * @method static Builder|Step query()
 * @method static Builder|Step whereCreatedAt( $value )
 * @method static Builder|Step whereId( $value )
 * @method static Builder|Step whereIsFinished( $value )
 * @method static Builder|Step wherePriority( $value )
 * @method static Builder|Step whereTaskId( $value )
 * @method static Builder|Step whereTitle( $value )
 * @method static Builder|Step whereUpdatedAt( $value )
 * @mixin Eloquent
 * @property-read \App\Models\Task $task
 * @method static \Database\Factories\StepFactory factory(...$parameters)
 */
class Step extends Model
{
    use HasFactory;

    /**
     * Get the task of this step
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * Create a new step for a specific task
     * @param array $data
     * @param int $task_id
     * @return Step
     * @throws Exception
     */
    public static function createFromData(array $data, int $task_id): Step
    {
        $step = Step::create([
            'title'    => $data['title'],
            'priority' => $data['priority'],
            'task_id'  => $task_id
        ]);
        if(is_null($step))
        {
            throw new Exception();
        }
        return $step;
    }

    /**
     * Mass assignable attributes
     * @var string[]
     */
    protected $fillable = [
        'title',
        'priority',
        'is_finished',
        'task_id'
    ];

    /**
     * No timestamps are needed
     * @var bool
     */
    public $timestamps = false;
}
