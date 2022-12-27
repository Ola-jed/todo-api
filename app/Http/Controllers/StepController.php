<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishRequest;
use App\Http\Requests\StepRequest;
use App\Models\Step;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class StepController
 * Steps of a task: CRUD
 * @package App\Http\Controllers
 */
class StepController extends Controller
{
    /**
     * Get all steps for a specific task
     * @param string $slug
     * @param Request $request
     * @return JsonResponse
     */
    public function all(string $slug, Request $request): JsonResponse
    {
        try
        {
            $user = $request->user();
            $task = $user->getTaskBySlug($slug);
            if (!$task->has_steps)
            {
                return response()->json([
                    'message' => 'Task does not have steps'
                ], Response::HTTP_FORBIDDEN);
            }
            $data = $task
                ->steps()
                ->get();
            return response()->json([
                'message' => 'Steps found',
                'data'    => $data,
                'count'   => $data->count()
            ]);
        }
        catch (Exception)
        {
            return response()->json([
                'message' => 'Not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Create a new step
     * @param string $slug
     * @param StepRequest $stepRequest
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function store(string $slug, StepRequest $stepRequest): \Illuminate\Http\Response
    {
        $user = $stepRequest->user();
        $task = $user->getTaskBySlug($slug);
        if (!$task->has_steps)
        {
            return response()->noContent(Response::HTTP_FORBIDDEN);
        }
        Step::createFromData($stepRequest->validated(), $task->id);
        return response()->noContent();
    }

    /**
     * Get a step
     * @param int $stepId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getStep(int $stepId, Request $request): \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
    {
        $user = $request->user();
        if (!$user->isStepAuthor($stepId))
        {
            return response()->noContent(Response::HTTP_NOT_FOUND);
        }
        return response()->json(Step::findOrFail($stepId));
    }

    /**
     * Update a step
     * @param int $stepId
     * @param StepRequest $request
     * @return JsonResponse
     */
    public function update(int $stepId, StepRequest $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->isStepAuthor($stepId))
        {
            return response()->json([
                'message' => 'Not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $stepToUpdate = Step::find($stepId);
        $hasUpdated = $stepToUpdate->update([
            'title'    => $request->input('title'),
            'priority' => $request->input('priority')
        ]);
        if (!$hasUpdated)
        {
            return response()->json([
                'message' => 'Could not update step'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json([
            'message' => 'Step updated',
            'data'    => $stepToUpdate
        ]);
    }

    /**
     * Set a step finished or not
     * @param int $stepId
     * @param FinishRequest $request
     * @return \Illuminate\Http\Response
     */
    public function finish(int $stepId, FinishRequest $request): \Illuminate\Http\Response
    {
        $user = $request->user();
        if (!$user->isStepAuthor($stepId))
        {
            return response()->noContent(Response::HTTP_NOT_FOUND);
        }
        $stepToFinish = Step::find($stepId);
        if (!$stepToFinish->update(['is_finished' => $request->input('status')]))
        {
            return response()->noContent(Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->noContent();
    }

    /**
     * Delete a step
     * @param int $stepId
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(int $stepId, Request $request): \Illuminate\Http\Response
    {
        $user = $request->user();
        if (!$user->isStepAuthor($stepId))
        {
            return response()->noContent(Response::HTTP_NOT_FOUND);
        }
        if (Step::destroy($stepId))
        {
            return response()->noContent();
        }
        return response()->noContent(Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
