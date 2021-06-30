<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TaskController
 * Manage tasks
 * @package App\Http\Controllers
 */
class TaskController extends Controller
{
    /**
     * Get all the tasks created by the user
     * @param Request $request
     * @return JsonResponse
     */
    public function all(Request $request): JsonResponse
    {
        $user = $request->user();
        $total = $user
            ->tasks()
            ->count();
        $limit = ($request->has('limit') && intval($request->input('limit')) > 0)
            ? intval($request->input('limit'))
            : $total;
        $offset = ($request->has('offset') && intval($request->input('offset')) > 0)
            ? intval($request->input('offset'))
            : 0;
        $data = $user->tasks()
            ->latest()
            ->limit($limit)
            ->offset($offset)
            ->get();
        $count = $data->count();
        return \response()->json([
           'data' => $data,
           'count' => $count,
           'remaining' => $total - $count
        ]);
    }

    /**
     * Create a new task
     * @param TaskRequest $taskRequest
     * @return JsonResponse
     */
    public function store(TaskRequest $taskRequest): JsonResponse
    {
        $user = $taskRequest->user();
        try
        {
            $newTask = Task::createFromData(
                $taskRequest->validated(),
                $user->id
            );
            return response()->json([
                'message' => 'Task created',
                'data' => $newTask
            ]);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Could not create the task'
            ],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Get a task
     * If it has steps show all the steps
     * @param string $slug
     * @param Request $request
     * @return JsonResponse
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $user = $request->user();
        try
        {
            $task = $user->getTaskBySlug($slug);
            if($task->has_steps)
            {
                return response()->json([
                    'message' => 'Task found',
                    'task' => $task,
                    'steps' => $task->steps()
                ]);
            }
            return response()->json([
                'message' => 'Task found',
                'task' => $task
            ]);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Not found'
            ],Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Search tasks by title
     * @param string $title
     * @param Request $request
     * @return JsonResponse
     */
    public function search(string $title, Request $request): JsonResponse
    {
        $user = $request->user();
        $tasks = $user->tasks()
            ->where('title','LIKE','%'.$title.'%')
            ->get();
        return response()->json([
            'data' => $tasks,
            'count' => $tasks->count()
        ]);
    }

    /**
     * Update a specific task
     * @param string $slug
     * @param TaskRequest $taskRequest
     * @return JsonResponse
     */
    public function update(string $slug, TaskRequest $taskRequest): JsonResponse
    {
        $user = $taskRequest->user();
        try
        {
            $task = $user->getTaskBySlug($slug);
            $hasUpdated = $task->updateWithData($taskRequest->validated());
            if($hasUpdated)
            {
                if(!$task->has_steps)
                {
                    $task->steps()
                        ->delete();
                }
                return response()->json([
                    'message' => 'Task updated',
                    'data' => $task
                ]);
            }
            return response()->json([
                'message' => 'Update error'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Not found'
            ],Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Set the is_finished status for a task
     * Can finish only if the steps of the tasks are finished
     * @param string $slug
     * @param FinishRequest $finishRequest
     * @return JsonResponse
     */
    public function finish(string $slug, FinishRequest $finishRequest): JsonResponse
    {
        $user = $finishRequest->user();
        try
        {
            $task = $user->getTaskBySlug($slug);
            if($finishRequest->input('status') && $task->has_steps)
            {
                $aStepUnfinished = $task->steps()
                    ->where('is_finished',false)
                    ->exists();
                if($aStepUnfinished)
                {
                    return response()->json([
                       'message' => 'All the steps are not yet finished'
                    ],Response::HTTP_FORBIDDEN);
                }
            }
            $task->is_finished = $finishRequest->input('status');
            $task->save();
            return response()->json([
                'message' => 'Task status updated',
                'data' => $task
            ]);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Not found'
            ],Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Deleting a task
     * If the user is their creator
     * @param string $slug
     * @param Request $deleteRequest
     * @return JsonResponse
     */
    public function delete(string $slug, Request $deleteRequest): JsonResponse
    {
        $user = $deleteRequest->user();
        try
        {
            $task = $user->getTaskBySlug($slug);
            if($task->delete())
            {
                return response()->json([
                    'message' => 'Task deleted'
                ]);
            }
            return response()->json([
                'message' => 'Deletion failed'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Not found'
            ],Response::HTTP_NOT_FOUND);
        }
    }
}
