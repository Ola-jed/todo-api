<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishRequest;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Carbon\Carbon;
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
            ->orderByDesc('id')
            ->limit($limit)
            ->offset($offset)
            ->get();
        $count = $data->count();
        return response()->json([
            'data'      => $data,
            'count'     => $count,
            'remaining' => $total - $count
        ]);
    }

    /**
     * Get the tasks finished by the user
     * @param Request $request
     * @return JsonResponse
     */
    public function getFinished(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
                ->tasks()
                ->orderByDesc('id')
                ->where('is_finished', true)
                ->get()
        ]);
    }

    /**
     * Get the tasks not finished by the user
     * @param Request $request
     * @return JsonResponse
     */
    public function getUnfinished(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
                ->tasks()
                ->orderByDesc('id')
                ->where('is_finished', false)
                ->get()
        ]);
    }

    /**
     * Get the expired tasks
     * @param Request $request
     * @return JsonResponse
     */
    public function getExpired(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()
                ->tasks()
                ->orderByDesc('id')
                ->where('date_limit', '<', Carbon::now()->toDate())
                ->get()
        ]);
    }

    /**
     * Create a new task
     * @param TaskRequest $taskRequest
     * @return JsonResponse
     * @throws \Exception
     */
    public function store(TaskRequest $taskRequest): JsonResponse
    {
        $user = $taskRequest->user();
        $newTask = Task::createFromData(
            $taskRequest->validated(),
            $user->id
        );
        return response()->json($newTask);
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
        $task = $user->getTaskBySlug($slug);
        if ($task->has_steps)
        {
            return response()->json([
                'task'  => $task,
                'steps' => $task->steps()
            ]);
        }
        return response()->json($task);
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
            ->orderByDesc('id')
            ->where('title', 'LIKE', '%' . $title . '%')
            ->get();
        return response()->json([
            'data'  => $tasks,
            'count' => $tasks->count()
        ]);
    }

    /**
     * Update a specific task
     * @param string $slug
     * @param TaskRequest $taskRequest
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function update(string $slug, TaskRequest $taskRequest): JsonResponse|\Illuminate\Http\Response
    {
        $user = $taskRequest->user();
        $task = $user->getTaskBySlug($slug);
        $hasUpdated = $task->updateWithData($taskRequest->validated());
        if ($hasUpdated)
        {
            if (!$task->has_steps)
            {
                $task->steps()->delete();
            }
            return response()->noContent();
        }
        return response()->noContent(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Set the is_finished status for a task
     * Can finish only if the steps of the tasks are finished
     * @param string $slug
     * @param FinishRequest $finishRequest
     * @return \Illuminate\Http\Response
     */
    public function finish(string $slug, FinishRequest $finishRequest): \Illuminate\Http\Response
    {
        $user = $finishRequest->user();
        $task = $user->getTaskBySlug($slug);
        if ($finishRequest->input('status') && $task->has_steps)
        {
            $aStepUnfinished = $task->steps()
                ->where('is_finished', false)
                ->exists();
            if ($aStepUnfinished)
            {
                return response()->noContent(Response::HTTP_FORBIDDEN);
            }
        }
        $task->is_finished = $finishRequest->input('status');
        $task->save();
        return response()->noContent();
    }

    /**
     * Deleting a task
     * If the user is their creator
     * @param string $slug
     * @param Request $deleteRequest
     * @return \Illuminate\Http\Response
     */
    public function delete(string $slug, Request $deleteRequest): \Illuminate\Http\Response
    {
        $user = $deleteRequest->user();
        $task = $user->getTaskBySlug($slug);
        if ($task->delete())
        {
            return response()->noContent();
        }
        return response()->noContent(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
