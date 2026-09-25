<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);

        return TaskResource::collection($project->tasks()->latest()->get());
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);
        $task = $project->tasks()->create($request->validate([
            'title' => ['required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_date' => ['nullable', 'date'],
        ]));

        return (new TaskResource($task->load('project')))->response()->setStatusCode(201);
    }

    public function show(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);

        return new TaskResource($task->load('project'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->update($request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'is_done' => ['sometimes', 'boolean'],
            'due_date' => ['nullable', 'date'],
        ]));

        return new TaskResource($task->fresh()->load('project'));
    }

    public function destroy(Request $request, Task $task)
    {
        $this->authorizeTask($request, $task);
        $task->delete();

        return response()->noContent();
    }

    private function authorizeProject(Request $request, Project $project): void
    {
        abort_unless($project->user()->is($request->user()), 403);
    }

    private function authorizeTask(Request $request, Task $task): void
    {
        abort_unless($task->project()->first()->user()->is($request->user()), 403);
    }
}