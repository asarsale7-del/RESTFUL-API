<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return ProjectResource::collection(
            $request->user()->projects()->withCount('tasks')->with('user')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string']]);

        return (new ProjectResource($request->user()->projects()->create($data)->load('user')))
            ->response()->setStatusCode(201);
    }

    public function show(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);

        return new ProjectResource($project->load('user', 'tasks')->loadCount('tasks'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);
        $project->update($request->validate(['name' => ['sometimes', 'required', 'string', 'max:255'], 'description' => ['nullable', 'string']]));

        return new ProjectResource($project->fresh()->load('user')->loadCount('tasks'));
    }

    public function destroy(Request $request, Project $project)
    {
        $this->authorizeProject($request, $project);
        $project->delete();

        return response()->noContent();
    }

    private function authorizeProject(Request $request, Project $project): void
    {
        abort_unless($project->user()->is($request->user()), 403);
    }
}