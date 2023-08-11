<?php

namespace App\Models;

use Database\Factories\StepFactory;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Step
 *
 * @property int $id
 * @property string $title
 * @property int $priority
 * @property bool $is_finished
 * @property int $task_id
 * @property-read Task $task
 * @method static StepFactory factory(...$parameters)
 * @method static Builder|Step newModelQuery()
 * @method static Builder|Step newQuery()
 * @method static Builder|Step query()
 * @method static Builder|Step whereId($value)
 * @method static Builder|Step whereIsFinished($value)
 * @method static Builder|Step wherePriority($value)
 * @method static Builder|Step whereTaskId($value)
 * @method static Builder|Step whereTitle($value)
 * @mixin Eloquent
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
     * @param int $taskId
     * @return Step
     * @throws Exception
     */
    public static function createFromData(array $data, int $taskId): Step
    {
        $step = Step::create([
            'title'    => $data['title'],
            'priority' => $data['priority'],
            'task_id'  => $taskId
        ]);
        if (is_null($step))
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
}
