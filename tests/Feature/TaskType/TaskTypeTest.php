<?php

namespace TaskType;

use App\Models\TaskType;
use Tests\TestCase;

class TaskTypeTest extends TestCase
{
    public function test_unauthenticated_user_can_not_render_TaskType_index_page()
    {
        $response = $this->get('/taskTypes');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_only_render_TaskType_index_page()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/taskTypes');

        $response->assertStatus(200);
        // $response->assertViewIs('task.type.index');
    }

    public function test_display_TaskType_info_in_index_page()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $TaskType = TaskType::create([
            'type' => 'Mery John 12345',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $TaskTypes = TaskType::with(['createdBy', 'updatedBy'])->get();

        $response = $this->get('/taskTypes');

        $response->assertStatus(200);
        // $response->assertViewIs('task.type.index');
        // $response->assertViewHas('task_types', $TaskTypes);
    }

    public function test_authenticated_non_SuperAdmin_canNot_render_TaskType_create_page()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);

        $response = $this->get('/taskTypes/create');

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_SuperAdmin_can_render_TaskType_create_page()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $response = $this->get('/taskTypes/create');
        $response->assertStatus(200);
        $response->assertViewIs('task.type.create');
    }

    public function test_authenticated_SuperAdmin_can_only_create_TaskType()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $response = $this->post('/taskTypes', [
            'task_type' => 'TaskType 1',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/taskTypes');
        $response->assertSessionHas('success', 'Task Type created successfully.');
    }

    public function test_authenticated_SuperAdmin_user_can_only_update()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $TaskType = TaskType::create([
            'type' => 'TaskType 2',
        ]);

        $response = $this->put('taskTypes/' . $TaskType->id, [
            'task_type' => 'TaskType Name Change',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/taskTypes');
        $response->assertSessionHas('success', 'Task Type updated successfully.');
    }

    public function test_authenticated_non_SuperAdmin_can_not_update()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);
        $TaskType = TaskType::create([
            'type' => 'TaskType 2',
        ]);

        $response = $this->put('taskTypes/' . $TaskType->id, [
            'task_type' => 'TaskType Name Change',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_SuperAdmin_user_can_only_render_edit_page()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $TaskType = TaskType::create([
            'type' => 'TaskType 2',
        ]);
        $response = $this->get('taskTypes/' . $TaskType->id . '/edit');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_not_render_edit_page_With_invalid_id()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);
        $TaskType = TaskType::create([
            'id' => 43,
            'type' => 'TaskType 2',
        ]);
        $random_id = 878;
        $response = $this->get('taskTypes/' . $random_id . '/edit');
        $response->assertStatus(404);
    }

    public function test_fail_for_invalid_data_when_update()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);
        $TaskType = TaskType::create([
            'type' => 'TaskType 2',
        ]);
        $response = $this->put('taskTypes/' . $TaskType->id);
        $response->assertStatus(302);

        $response->assertSessionHasErrors(
            ['task_type' => 'The task type field is required.'],
        );
    }

    public function test_fail_for_invalid_data_when_store()
    {
        $user = \App\Models\User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $this->actingAs($user);

        $response = $this->post('taskTypes/');
        $response->assertStatus(302);
        $response->assertSessionHasErrors(
            ['task_type' => 'The task type field is required.'],
        );
    }
}
