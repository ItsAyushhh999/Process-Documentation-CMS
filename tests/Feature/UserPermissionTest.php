<?php

namespace Feature;

use App\Models\Permission;
use App\Models\PermissionUser;
use App\Models\Project;
use App\Models\User;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    public function test_user_permission()
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);
        $permissions = Permission::factory(3)->create();

        $project = Project::factory()->create();
        Project::factory(3)->create([
            'sub_projects' => $project->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->get("/permissions/$user->id/edit");
        $response->assertStatus(200);
        $response->assertSee($project->name);

        foreach ($permissions as $permission) {
            $response->assertSee($permission->name);
        }
    }

    public function test_user_permission_unauthorized()
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        Permission::factory(3)->create();

        $project = Project::factory()->create();
        Project::factory(3)->create([
            'sub_projects' => $project->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $response = $this->get("/permissions/$user->id/edit");
        $response->assertStatus(302);
    }

    public function test_user_permission_submit()
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        // Number of posts before the insert
        $beforeInsertCount = PermissionUser::count();

        $this->actingAs($user);
        $permissions = Permission::factory(3)->create();
        $project = Project::factory()->create();

        $subProjects = Project::factory(3)->create([
            'sub_projects' => $project->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $data = [];
        foreach ($subProjects as $key => $subProject) {
            foreach ($permissions as $permission) {
                $data[] = "$permission->id:$subProject->id";
            }
        }
        $formData['permissions'] = $data;

        $response = $this->patch("/permissions/$user->id/update", $formData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $afterInsertCount = PermissionUser::count();
        $this->assertEquals($beforeInsertCount + 9, $afterInsertCount);
        $this->assertDatabaseCount('permission_user', $beforeInsertCount + 9);
    }

    public function test_user_permission_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->actingAs($user);
        $permissions = Permission::factory(3)->create();
        $project = Project::factory()->create();

        $subProjects = Project::factory(3)->create([
            'sub_projects' => $project->id,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $data = [];

        foreach ($subProjects as $key => $subProject) {
            foreach ($permissions as $permission) {
                PermissionUser::insert([
                    'user_id' => $user->id,
                    'project_id' => $subProject->id,
                    'permission_id' => $permission->id,
                ]);
            }
        }

        // Number of posts before the insert
        $beforeInsertCount = PermissionUser::count();

        $formData['permissions'] = $data;

        $response = $this->patch("/permissions/$user->id/update", $formData);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals($beforeInsertCount, 9);
        $this->assertDatabaseCount('permission_user', 0);
    }
}
