<?php

namespace Tests\Feature\dashboard;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskStatus;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_unauthenticated_user_can_not_render_dashboard_page()
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_render_dashboard_page()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        // $response->assertViewIs('dashboard');
    }

    public function test_list_of_task_in_index_page()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'Project one is all about Git Automation',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()->addDays(3),
            'created_by' => '1',
        ]);

        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);
        $task = Task::create([
            'title' => 'Task 1',
            'description' => 'Task 1 is important',
            'project_id' => $project->id,
            'priority' => '0',
            'created_at' => '2023-05-31',
            'deadline' => Carbon::now(),
            'status' => '0',
            'createdBy' => $user->id,
            'updatedBy' => $user->id,
        ]);
        $assigned = TaskCollaborator::create([
            'taskId' => $task->id,
            'collaborator' => $user->id,
            'flag' => '0',
        ]);
        $reviewer = TaskCollaborator::create([
            'taskId' => $task->id,
            'collaborator' => $user->id,
            'flag' => '1',
        ]);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        // $response->assertViewIs('dashboard');
        // $response->assertViewHas('data', function ($data) use ($task) {
        //     return $data->contains('title', $task->title);
        // });
        // $response->assertViewHas('data', function ($data) use ($task) {
        //     return $data->contains('created_at', $task->created_at);
        // });
        // $response->assertViewHas('data', function ($data) use ($task) {
        //     return $data->contains('priority', $task->priority);
        // });

        // $response->assertViewHas('data', function ($data) use ($task) {
        //     return $data->contains('status', $task->status);
        // });

        // $response->assertViewHas('data', function ($data) use ($user) {
        //     return $data->contains('createdBy', $user->name);
        // });

        // $response->assertViewHas('data', function ($data) use ($project) {
        //     return $data->contains('projectName', $project->name);
        // });
        // $response->assertViewHas('data', function ($data) use ($assigned) {
        //     return $data->contains('isAssignee', $assigned->flag);
        // });
        // $response->assertViewHas('data', function ($data) use ($reviewer) {
        //     return $data->contains('isReviewer', $reviewer->flag);
        // });
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/logout');
        $response->assertRedirect('/');
    }
}
