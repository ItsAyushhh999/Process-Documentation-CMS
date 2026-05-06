<?php

namespace Tests\Feature\Api\Project;

use App\Models\Department;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_listing_document()
    {
        $department = Department::create([
            'department_name' => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'id' => 5,
            'is_super_admin' => '1',
            'department_id' => $department['id'],
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $document = Document::create([
            'name' => 'Document One',
            'description' => 'Document one is important',
            'project_id' => $project['id'],
            'isPublish' => 1,
        ]);

        $response = $this->get('/api/project/' . $project['id'] . '/documents');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Documents fetched successfully.',
        ]);
    }

    public function test_listing_document_with_invalidId()
    {
        $department = Department::create([
            'department_name' => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'id' => 5,
            'is_super_admin' => '1',
            'department_id' => $department['id'],
        ]);

        $this->actingAs($user);

        $invalidId = 0;
        $response = $this->get('/api/project/' . $invalidId . '/documents');
        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Project not found',
        ]);
    }

    public function test_project_document_detail()
    {

        $department = Department::create([
            'department_name' => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'id' => 5,
            'is_super_admin' => '1',
            'department_id' => $department['id'],
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $document = Document::create([
            'name' => 'Document One',
            'description' => 'Document one is important',
            'project_id' => $project['id'],
            'isPublished' => 1,
        ]);

        $response = $this->get('/api/document/' . $document['id']);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Document detail fetched successfully.',
        ]);
    }

    public function test_project_document_detail_with_invalidId()
    {

        $department = Department::create([
            'department_name' => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'id' => 5,
            'is_super_admin' => '1',
            'department_id' => $department['id'],
        ]);

        $this->actingAs($user);

        $inValidId = 0;
        $response = $this->get('/api/document/' . $inValidId);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'error',
            'message' => 'Document not found',
        ]);
    }
}
