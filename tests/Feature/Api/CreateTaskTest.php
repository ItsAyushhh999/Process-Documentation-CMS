<?php

namespace Tests\Feature\Api;

use App\Models\Project;
use App\Models\TaskStatus;
use App\Models\TaskType;
use App\Models\User;
use App\Services\NotificationService;
use Aws\Api\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function test_create_task()
    {
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
            'id' => 5,
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

        $data = [
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
            'type' => $taskType,
            'reviewers'  => [$user1->id],
            'assignees' => [$user2->id],
        ];
        $response = $this->post('/api/tasks/create', $data);
        $response->assertStatus(200);
    }

    public function test_list_user_task_type_projects()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);
        $response = $this->get('api/resource');
        $response->assertStatus(200);
    }
}
