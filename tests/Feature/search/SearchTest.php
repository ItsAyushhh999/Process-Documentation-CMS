<?php

namespace Tests\Feature\search;

use App\Models\Document;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testSearchProjects()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $taskStatus = TaskStatus::Create([
            'name' => 'Assigned',
            'value' => '0',
        ]);

        $task = Task::create([
            'title' => 'Title one',
            'description'   => 'Description is important',
            'project_id'    => $project->id,
            'priority'      => '0',
            'status'        => '0',
            'deadline'      => now()->addDays(4),
            'createdBy'     => $user->id,
            'updatedBy'     => $user->id,
        ]);
        $response = $this->get('/search?type=projects&search=script');
        // $response->assertViewIs('search.result');
        $response->assertStatus(200);
    }

    public function testSearchDocument()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        Document::factory()->create([
            'project_id'    => $project->id,
        ]);

        $response = $this->get('/search?type=documents&search=ume');
        // $response->assertViewIs('search.result');
        $response->assertStatus(200);
    }
}
