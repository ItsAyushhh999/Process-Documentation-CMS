<?php

namespace Tests\Feature\Task\DeadlinePass;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_only_view_task_with_passed_deadline()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/tasks/deadline');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_can_not_view_task_with_passed_deadline()
    {
        $response = $this->get('/tasks/deadline');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_displaying_deadlined_pass_task_in_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $taskType = TaskType::factory()->create([
            'type'  => 'Bugs',
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
        ]);
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);
        $task = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '0',
        ]);

        $task->update([
            'deadline'  => now()->subDays(10),
        ]);
        $task->save();

        $response = $this->get('/tasks/deadline');

        $response->assertStatus(200);

        $response->assertSee('tasks');
    }
}
