<?php

namespace Department;

use App\Models\Department;
use App\Models\User;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    public function test_unauthenticated_user_can_not_render_department_index_page()
    {
        $response = $this->get('/departments');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_only_render_department_index_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/departments');

        $response->assertStatus(200);
        // $response->assertViewIs('department.index');
    }

    public function test_display_department_info_in_index_page()
    {
        $user = User::factory()->create();
        $department = Department::create([
            'department_name' => 'Mery John 12345',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $this->actingAs($user);

        $departments = Department::with(['createdBy', 'updatedBy'])->get();

        $response = $this->get('/departments');

        $response->assertStatus(200);
        // $response->assertViewIs('department.index');
        // $response->assertViewHas('departments', $departments);
    }

    public function test_authenticated_non_SuperAdmin_canNot_render_department_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->get('/departments/create');

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_SuperAdmin_can_render_department_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/departments/create');

        $response->assertStatus(200);
        $response->assertViewIs('department.create');
    }

    public function test_authenticated_SuperAdmin_can_only_create_department()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post('/departments', [
            'department_name' => 'department 1',
        ]);

        $response->assertStatus(302);
        // $response->assertRedirect('/departments');
        $response->assertSessionHas('success', 'Department created successfully.');
    }

    public function test_authenticated_user_without_superAdmin_can_not_create_department()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->post('/departments', [
            'department_name' => 'department1',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_authenticated_SuperAdmin_user_can_only_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $department = Department::create([
            'department_name' => 'department 2',
        ]);

        $response = $this->put('departments/' . $department->id, [
            'department_name' => 'department Name Change',
        ]);

        $response->assertStatus(302);
        // $response->assertRedirect('/departments');
        $response->assertSessionHas('success', 'Department updated successfully.');
    }

    public function test_authenticated_with0ut_SuperAdmin_user_can_not_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);

        $this->actingAs($user);

        $department = Department::create([
            'department_name' => 'department 2',
        ]);

        $response = $this->put('departments/' . $department->id, [
            'department_name' => 'department Name Change',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_SuperAdmin_user_can_only_render_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $department = Department::create([
            'department_name' => 'department 2',
        ]);

        $this->actingAs($user);

        $response = $this->get('departments/' . $department->id . '/edit');

        $response->assertStatus(200);
    }

    public function test_authenticated_non_superAdmin_can_not_render_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $department = Department::create([
            'department_name' => 'department 2',
        ]);

        $this->actingAs($user);

        $response = $this->get('departments/' . $department->id . '/edit');

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_user_can_not_render_edit_page_With_invalid_id()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $department = Department::create([
            'id' => 43,
            'department_name' => 'department 2',
        ]);

        $this->actingAs($user);

        $random_id = 878;
        $response = $this->get('departments/' . $random_id . '/edit');
        $response->assertStatus(404);
    }

    public function test_fail_for_invalid_data_when_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $department = Department::create([
            'department_name' => 'department 2',
        ]);

        $this->actingAs($user);

        $response = $this->put('departments/' . $department->id);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(
            ['department_name' => 'The department name field is required.'],
        );
    }

    public function test_fail_for_invalid_data_when_store()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post('departments/');

        $response->assertStatus(302);
        $response->assertSessionHasErrors(
            ['department_name' => 'The department name field is required.'],
        );
    }
}
