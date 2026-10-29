<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\TaskCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_category_can_be_created(): void
    {
        $response = $this->postJson('/api/task-categories', [
            'name' => 'Work',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('name', 'Work');

        $this->assertDatabaseHas('task_categories', ['name' => 'Work']);
    }

    public function test_task_category_can_be_updated(): void
    {
        $category = TaskCategory::create(['name' => 'Work']);

        $this->patchJson("/api/task-categories/{$category->id}", [
            'name' => 'Personal',
        ])
            ->assertOk()
            ->assertJsonPath('id', $category->id)
            ->assertJsonPath('name', 'Personal');

        $this->assertDatabaseHas('task_categories', [
            'id' => $category->id,
            'name' => 'Personal',
        ]);
    }

    public function test_task_category_update_rejects_duplicate_names(): void
    {
        $category = TaskCategory::create(['name' => 'Work']);
        TaskCategory::create(['name' => 'Personal']);

        $this->patchJson("/api/task-categories/{$category->id}", [
            'name' => 'Personal',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);

        $this->assertDatabaseHas('task_categories', [
            'id' => $category->id,
            'name' => 'Work',
        ]);
    }

    public function test_task_can_be_created_with_a_category(): void
    {
        $category = TaskCategory::create(['name' => 'Work']);

        $response = $this->postJson('/api/tasks', [
            'title' => 'Prepare report',
            'description' => 'Draft the weekly report.',
            'due_date' => '2026-11-01',
            'task_category_id' => $category->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('title', 'Prepare report')
            ->assertJsonPath('status', 'pending')
            ->assertJsonPath('task_category.id', $category->id);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Prepare report',
            'task_category_id' => $category->id,
        ]);
    }

    public function test_task_creation_validates_required_and_related_fields(): void
    {
        $this->postJson('/api/tasks', [
            'task_category_id' => 999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'task_category_id']);

        $this->assertSame(0, Task::count());
    }

    public function test_api_validation_errors_are_json_without_an_accept_header(): void
    {
        $response = $this->post('/api/tasks', [
            'name' => 'policy',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertSame(0, Task::count());
    }
}
