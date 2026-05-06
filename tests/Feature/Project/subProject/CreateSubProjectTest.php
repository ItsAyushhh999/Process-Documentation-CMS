<?php

namespace Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateSubProjectTest extends TestCase
{
    public function test_authenticated_user_can_only_create_sub_project()
    {

        //Creating User
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        //authenticating the user
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'Warehouse Project',
            'description' => 'Warehouse  Project is important',
            'url' => 'https://laravel.com',
            'created_by' => Auth::user()->id,
        ]);

        $request = [
            'project_id_for_sub_project' => $project->id,
            'name' => 'Project one',
            'description' => 'Description one is important',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ];

        $response = $this->post('/projects', $request);
        $response->assertStatus(302);
    }

    public function test_authenticated_user_can_only_update_sub_projects()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $project = Project::create([
            'name' => 'Sub Warehouse Project',
            'description' => 'Warehouse  Project is important',
            'url' => 'https://laravel.com',
            'branch' => 'production',
            'created_by' => Auth::user()->id,
        ]);

        $requestData = [
            'name' => 'Project one',
            'description' => 'Description one is important',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'branch' => 'production',
            'created_by' => Auth::user()->id,
        ];

        $response = $this->put('projects/' . $project->id, [
            'name' => 'Project one',
            'description' => 'Description one is important',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'branch' => 'production',
            'created_by' => Auth::user()->id,
        ]);
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Project Updated Successfully');
    }
}
