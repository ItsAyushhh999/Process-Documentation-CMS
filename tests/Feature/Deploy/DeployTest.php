<?php

namespace Project;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use Tests\TestCase;

class DeployTest extends TestCase
{
    public function test_deploy_and_deploy_logs_button_can_only_view_by_superAdmin()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $taskType = TaskType::factory()->create([
            'type'  => 'Bugs',
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);
        $task = Task::create([
            'title' => 'Title one',
            'description' => 'Description is important',
            'project_id' => $project->id,
            'priority' => '0',
            'deadline' => now()->addDays(4),
            'createdBy' => $user1->id,
            'updatedBy' => $user2->id,
            'status' => '0',
        ]);
        $response = $this->get('tasks/' . $task->id . '/edit');
        $response->assertSee('Deploy');

    }

    // public function test_deploy_and_deploy_logs_button_can_not_view_by_authenticate_user_without_superAdmin() {
    //     $user = User::factory()->create([
    //         'is_super_admin' => 0,
    //     ]);

    //     $this->actingAs($user);

    //     $project = Project::factory()->create();

    //     $taskType = TaskType::factory()->create([
    //         'type'  => 'Bugs',
    //         'created_by'    => $user->id,
    //         'updated_by'    => $user->id,
    //     ]);

    //     $user1 = User::factory()->create();
    //     $user2 = User::factory()->create();

    //     $taskStatus = TaskStatus::Create([
    //         'name' => 'Assigned',
    //         'value' => '0',
    //     ]);

    //     $task = Task::create([
    //         'title' => 'Title one',
    //         'description' => 'Description is important',
    //         'project_id' => $project->id,
    //         'priority' => '0',
    //         'deadline' => now()->addDays(4),
    //         'createdBy' => $user1->id,
    //         'updatedBy' => $user2->id,
    //         'status' => '0',
    //     ]);
    //     $response = $this->get('tasks/'.$task->id.'/edit');
    //     $response->assertStatus(200);

    //     $response->assertDontSee('Deploy');
    //     $response->assertDontSee('Deploy Logs');
    // }

    public function test_authenticate_user_can_only_render_deploy_Log_list()
    {

        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/deploy/log_list');

        $response->assertStatus(302);
    }

    public function test_unauthenticate_user_can_not_render_deploy_Log_list()
    {

        $response = $this->get('/deploy/log_list');

        $response->assertStatus(302);
        $response->assertRedirect();
    }
}
