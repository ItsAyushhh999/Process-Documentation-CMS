<?php

namespace Project;

use App\Models\Category;
use App\Models\Department;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ProjectsTest extends TestCase
{
    public function test_authenticated_user_can_only_render_project_index_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/projects');

        $response->assertStatus(200);
        // $response->assertViewIs('project.index');
    }

    public function test_unauthenticated_user_can_not_render_project_index_page()
    {
        $response = $this->get('/projects');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_unauthenticated_user_can_not_render_projects_create_page()
    {
        $response = $this->get('/projects/create');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_superAdmin_can_only_render_projects_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $response = $this->get('/projects/create');

        $response->assertStatus(200);
        // $response->assertViewIs('project.create');
    }

    public function test_authenticated_non_superAdmin_can_not_render_projects_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);

        $response = $this->get('/projects/create');

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');

        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_superAdmin_user_can_store_project()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $requestData = [
            'name' => 'Project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ];

        $response = $this->post('/projects', $requestData);
        $response->assertStatus(302);

        $response->assertRedirect(route('projects.index'));
        $response->assertSessionHas('success', 'Poject added Successfully.');

        $this->assertDatabaseHas('projects', [
            'name' => $requestData['name'],
            'description'   => $requestData['description'],
            'url'   => $requestData['url'],
        ]);
    }

    public function test_authenticated_user_without_superAdmin_privileges_can_not_create_project()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 0,
        ]);

        $this->actingAs($user);
        $requestData = [
            'name' => 'Project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ];

        $response = $this->post('/projects', $requestData);
        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
    }

    public function test_displaying_project()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $project = Project::create([
            'name' => 'Project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' =>$user->id,
        ]);

        $documents = Document::create([
            'name' => 'Documents 1',
            'project_id' => $project->id,
            'description' => 'First Document For Project Mission Impossible',
        ]);

        $documentId = Document::get('id')->first();
        $response = $this->get('/projects');
        $response->assertStatus(200);
        // $response->assertViewIs('project.index');
        $response->assertInertia(function ($page) use ($project, $documents) {
            $page->component('Projects/index')
                 ->where('projects', function ($projects) use ($project) {
                     return $projects->contains('id', $project->id);
                 })
                 ->where('projects', function ($projects) use ($project) {
                     return $projects->contains('name', $project->name);
                 })
                 ->where('projects', function ($projects) use ($project) {
                     return $projects->contains('url', $project->url);
                 });

            return true;
        });

    }

    public function test_unauthenticated_user_can_not_render_project_documents()
    {
        $response = $this->get('project/1/document');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_render_project_documents()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'Project 2',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ]);
        $response = $this->get('project/' . $project->id . '/document');

        $response->assertStatus(200);

        // $response->assertViewIs('documents.show');
    }

    public function test_authenticated_user_can_not_render_project_documents_with_invalid_project_id()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $project = Project::create([
            'id' => 1,
            'name' => 'Project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ]);
        $invalid_id = 111;

        $response = $this->get('/project/' . $invalid_id . '/document');

        $response->assertStatus(302);
        $response->assertRedirect(route('projects.index'));

        $response->assertSessionHas('error', 'Project not found.');
    }

    public function test_unauthenticated_user_cannot_render_project_edit_page()
    {
        $response = $this->get('/projects/1/edit');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_superAdmin_can_render_project_edit_page()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ]);

        $response = $this->get('projects/' . $project->id . '/edit');

        $response->assertStatus(200);

    }

    public function test_authenticated_non_superAdmin_can_not_render_project_edit_page()
    {
        $user = User::factory()->create([
            'name' => 'Mery John',
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);
        $project = Project::create([
            'name' => 'project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ]);
        $response = $this->get('projects/' . $project->id . '/edit');

        $response->assertStatus(302);
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_user_cannot_render_project_edit_page_without_valid_id()
    {
        $user = User::factory()->create([
            'name' => 'Merry John',
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);

        $project = Project::create([
            'name' => 'project 1',
            'description' => 'About Project 1',
            'url' => 'http://127.0.0.1:8000/projects/create',
            'created_by' => Auth::user()->id,
        ]);

        $random_id = 0;
        $response = $this->get('projects/' . $random_id . '/edit');

        $response->assertStatus(404);
    }

    public function test_update_project()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $project = Project::factory()->create();

        $requestData = [
            'name' => 'Update Project Name',
            'description' => 'Update project description',
            'url'   => 'https://laravel.com/docs/10.x',
            'updated_by' => Auth::user()->id,
        ];

        $response = $this->put('projects/' . $project->id, [
            'name'  => $requestData['name'],
            'description'   => $requestData['description'],
            'url'   => $requestData['url'],
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Project Updated Successfully');

        $this->assertDatabaseHas('projects', [
           'name' => $requestData['name'],
           'description' => $requestData['description'],
           'url'    => $requestData['url'],
        ]);
    }

    public function test_create_document()
    {
        $department = Department::create([
            'department_name' => 'Project Documentation',
        ]);

        $user = User::factory()->create([
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $categories = Category::create([
            'name'  => 'Category one',
            'description'   => 'Category one is important',
            'project'   => $project->id,
        ]);

        $requestData = [
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document one is important',
        ];

        $response = $this->post('/document', [
            'name'  => $requestData['name'],
            'description'   => $requestData['description'],
            'categories'    => $categories->id,
            'projectId'    => $requestData['project_id'],
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('documents', [
           'name' => $requestData['name'],
           'description'    => $requestData['description'],
        ]);
    }

    public function test_user_can_not_create_document_with_out_enough_privileges()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $project = Project::factory()->create();

        $categories = Category::create([
            'name'  => 'Category one',
            'description'   => 'Category one is important',
            'project'   => $project->id,
        ]);

        $requestData = [
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document one is important',
        ];

        $response = $this->post('/document', [
            'name'  => $requestData['name'],
            'description'   => $requestData['description'],
            'categories'    => $categories->id,
            'projectId'    => $requestData['project_id'],
        ]);

        $response->assertStatus(302)->assertRedirect()->assertSessionHas('error', 'Not enough privileges.');
    }

    public function test_update_document()
    {
        $department = Department::create([
            'department_name' => 'Project Documentation',
        ]);

        $user = User::factory()->create([
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $categories = Category::create([
            'name'  => 'Category one',
            'description'   => 'Category one is important',
            'project'   => $project->id,
        ]);

        $document = Document::create([
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document One is important',
            'createdBy'   => Auth::user()->id,
        ]);

        $requestData = [
          'name'    => 'Update document one',
          'description' => 'Updated description',
          'categories' => $categories->id,
        ];

        $response = $this->put('document/' . $document->id, [
            'name' =>  $requestData['name'],
            'description'   => $requestData['description'],
            'categories' => $categories->id,
            'isPublish' => 1,
        ]);
        $response->assertStatus(302);

        $this->assertDatabaseHas('documents', [
            'name' => $requestData['name'],
            'description'   => $requestData['description'],
        ]);
    }

    public function test_user_can_not_update_document_with_out_enough_privileges()
    {
        $department = Department::create([
            'department_name' => 'Project Documentation',
        ]);

        $user = User::factory()->create([
            'is_super_admin'    => 1,
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $categories = Category::create([
            'name'  => 'Category one',
            'description'   => 'Category one is important',
            'project'   => $project->id,
        ]);

        $requestData = [
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document one is important',
        ];

        $document = Document::create([
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document One is important',
            'createdBy'   => Auth::user()->id,
        ]);

        $response = $this->put('document/' . $document->id, [
            'name' =>  $requestData['name'],
            'description'   => $requestData['description'],
            'categories' => $categories->id,
            'isPublish' => 1,
        ]);

        $response->assertStatus(302)->assertRedirect();
    }

    public function test_document_status()
    {
        $department = Department::create([
            'department_name' => 'Project Documentation',
        ]);

        $user = User::factory()->create([
            'is_super_admin'    => 1,
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $project = Project::factory()->create();

        $categories = Category::create([
            'name'  => 'Category one',
            'description'   => 'Category one is important',
            'project'   => $project->id,
        ]);

        $document = Document::create([
            'name'        => 'Document one',
            'project_id'  => $project->id,
            'description' => 'Document One is important',
            'createdBy'   => Auth::user()->id,
            'isPublished' => 0,
        ]);

        $response = $this->post('document/status', [
            'documentId' => $document['id'],
        ]);
        $response->assertStatus(302);
    }

    // public function test_document_order_update()
    // {
    //     $user = User::factory()->create([
    //         'is_super_admin'    => 1.
    //     ]);

    //     $this->actingAs($user);

    //     $project = Project::factory()->create();

    //     $document = Document::factory()->count(5)->create([
    //         'project_id' => $project->id,
    //     ]);

    //     $data = [
    //         ['id' => $document[0]->id,'position' => 5],
    //         ['id' => $document[1]->id, 'position' => 4],
    //         ['id' => $document[2]->id, 'position' => 3],
    //         ['id' => $document[3]->id, 'position' => 2],
    //         ['id' => $document[4]->id,'position' => 1]
    //     ];

    //     $response = $this->put('/document/position/update',['body' => $data]);
    //     $response->assertStatus(200);
    // }
}
