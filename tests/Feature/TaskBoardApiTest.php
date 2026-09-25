<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskBoardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_create_a_project(): void
    {
        $registration = $this->postJson('/api/register', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $registration->assertCreated()->assertJsonStructure(['user', 'token']);

        $this->withToken($registration->json('token'))
            ->postJson('/api/projects', ['name' => 'Capstone Prep'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Capstone Prep');
    }

    public function test_project_detail_includes_owner_and_tasks(): void
    {
        $user = User::factory()->create();
        $project = $user->projects()->create(['name' => 'Capstone Prep']);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/projects/{$project->id}/tasks", ['title' => 'Draft ERD'])
            ->assertCreated()
            ->assertJsonPath('data.is_done', false);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/projects/{$project->id}")
            ->assertOk()
            ->assertJsonPath('data.owner.email', $user->email)
            ->assertJsonPath('data.tasks.0.title', 'Draft ERD')
            ->assertJsonMissingPath('data.owner.password');
    }

    public function test_another_user_cannot_access_a_project(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $project = $owner->projects()->create(['name' => 'Private']);

        $this->actingAs($otherUser, 'sanctum')
            ->getJson("/api/projects/{$project->id}")
            ->assertForbidden();
    }

    public function test_projects_require_sanctum_authentication(): void
    {
        $this->getJson('/api/projects')->assertUnauthorized();
    }
}