<?php

namespace Tests\Feature\CronJob;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CronJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_archiveCompletedTasks()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);
        $taskStatus = TaskStatus::Create([
            'name' => 'Archive Task',
            'value' => '5',
        ]);
        $task = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '5',
            'taskEndedAt'   => Carbon::now()->subDays('15')->endOfDay(),
        ]);

        $response = $this->get('/cronJobs/archiveCompletedTasks');

        $response->assertStatus(200);
        $response->assertSee('Tasks has been archived.');
    }

    public function test_uncompletedTask()
    {

        Mail::fake();
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);

        $task1 = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '0',
        ]);

        $task2 = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '0',
        ]);

        $task3 = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->subDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '0',
        ]);

        $response = $this->get('/cronJobs/uncompletedTask');

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Email sent successfully.');
    }

    public function test_notifyTasks()
    {
        Mail::fake();
        Http::fake(['https://slack.com/api/chat.postMessage' => Http::response(['key'=> 'value'], 200)]);

        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();
        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);
        $task1 = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
            'status'        => '0',
        ]);

        $response = $this->get('/cronJobs/notifyTasks');
        $response->assertStatus(200);
    }
}
