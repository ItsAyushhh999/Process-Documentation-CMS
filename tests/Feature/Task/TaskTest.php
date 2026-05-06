<?php

namespace Tests\Feature\Task;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use App\Services\NotificationService;
use Aws\Api\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_render_task_index_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/tasks');

        $response->assertStatus(200);

    }

    public function test_unauthenticated_user_can_not_render_task_index_page()
    {
        $response = $this->get('/tasks');

        $response->assertStatus(302);
        // $response->assertRedirect('http://127.0.0.1:8000');
    }

    public function test_authenticated_user_can_only_render_task_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin'    => 1,
        ]);
        $this->actingAs($user);

        $response = $this->get('/tasks/create');

        $response->assertStatus(200);
        $response->assertViewIs('task.create');
    }

    public function test_unauthenticated_user_can_not_render_task_create_page()
    {
        $response = $this->get('/tasks/create');

        $response->assertStatus(302);
        // $response->assertRedirect('http://127.0.0.1:8000');
    }

    public function test_createTask()
    {
        // Mock the HTTP request
        Http::fake([
            'https://slack.com/api/chat.postMessage' => Http::response(['key' => 'value'], 200),
        ]);

        $this->instance(
            Service::class,
            Mockery::mock(NotificationService::class, function (MockInterface $mock) {
                $mock->shouldReceive('notifyUser')->with([
                    'mukesh', 'keshab', 'merry', 'john',
                ]);
            })
        );
        // Mock the Mail::send method
        Mail::fake();

        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $taskType = TaskType::factory()->create([
            'type'  => 'Bugs',
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
        ]);

        //Uploading Attachments my creating Fake Storage
        Storage::fake('task');
        UploadedFile::fake()->image('fakeImage.png')->storeAs('', 'fakeImage.png', 'task');
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);

        $response = $this->post('/tasks', [
            'title' => 'Title one',
            'description'    => 'Description is important',
            'project_id' => $project->id,
            'priority'   => '0',
            'deadline'   => now()->addDays(2),
            'createdBy' => $user->id,
            'updatedBy'  => $user->id,
            'status'     => '0',
            'assignedAt' => now(),
            'assignedBy' => $user->id,
            'task_type' => [$taskType['id']],
            'reviewer'  => [$user1->id],
            'assignees' => [$user2->id],
        ]);
        $response->assertSessionHas('success', 'Task added Successfully.');
        $response->assertStatus(302);
        $response->assertRedirect('/tasks');

        Storage::disk('task')->assertExists('fakeImage.png');
    }

    public function test_createTask_without_enough_privileges()
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

        $taskStatus = TaskStatus::Create([
            'name' => 'Assigne',
            'value' => '7',
        ]);
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $response = $this->post('/tasks', [
            'title' => 'Title one',
            'description'    => 'Description is important',
            'project_id' => $project->id,
            'priority'   => '0',
            'deadline'   => now()->addDays(2),
            'createdBy' => $user->id,
            'updatedBy'  => $user->id,
            'status'     => '0',
            'assignedAt' => now(),
            'assignedBy' => $user->id,
            'task_type' => $taskType,
            'reviewers'  => [$user1->id],
            'assignees' => [$user2->id],
        ]);
        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_update_task_only_when_user_is_superAdmin()
    {
        // Mock the HTTP request
        Http::fake([
            'https://slack.com/api/chat.postMessage' => Http::response(['key' => 'value'], 200),
        ]);

        // Mock the Mail::send method
        Mail::fake();

        Storage::fake('task');
        UploadedFile::fake()->image('updatedTask.png')->storeAs('', 'updatedTask.png', 'task');

        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
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
        $response = $this->put('/tasks/' . $task->id, [
            'title' => 'Title one',
            'description'    => 'Description is important',
            'project_id' => $project->id,
            'priority'   => '0',
            'deadline'   => now()->addDays(2),
            'createdBy' => $user->id,
            'updatedBy'  => $user->id,
            'status'     => '0',
            'assignedAt' => now(),
            'assignedBy' => $user->id,
            'task_type' => [$taskType['id']],
            'reviewers'  => [$user1->id],
            'assignees' => [$user2->id],
        ]);
        $response->assertSessionHas('success', 'Task details updated successfully.');
        $response->assertStatus(302);
    }
}
