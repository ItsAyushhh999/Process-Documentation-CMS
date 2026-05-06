<?php

namespace Tests\Feature;

use App\Models\TaskStatus;
use App\Models\User;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    public function testListTaskStatuses()
    {
        $user = User::factory()->create();
        $taskStatuses = TaskStatus::factory(10)->create();

        $this->actingAs($user);

        $response = $this->get('/taskStatuses');
        $response->assertStatus(200);
        $response->assertInertia(function ($page) use ($taskStatuses) {
            return $page->component('TaskStatuses/index')
                ->where('taskStatuses', function ($statuses) use ($taskStatuses) {
                    foreach ($taskStatuses as $status) {
                        if (!$statuses->contains('id', $status->id) ||
                            !$statuses->contains('name', $status->name) ||
                            !$statuses->contains('value', $status->value)) {
                            return false;
                        }
                    }

                    return true;
                });
        });

    }

    public function testCreateStatusWithoutAuthorization()
    {
        $user = User::factory()->create(['is_super_admin' => 0]);

        $this->actingAs($user);

        $response = $this->get('/taskStatuses/create');

        $response->assertRedirect();

        // Assert that the redirect location is the 'dashboard' route
        $response->assertRedirect(route('dashboard'));

        // Assert that the session has the 'error' key with the specified message
        $response->assertSessionHas('error', 'Unauthorized Action.');

        // for store route
        $response = $this->post(route('taskStatuses.store'), [
            'name' => 'test',
            'value' => '1',
        ]);

        // Assert that the redirect location is the 'dashboard' route
        $response->assertRedirect(route('dashboard'));

        // Assert that the session has the 'error' key with the specified message
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function testCreateStatusWithAuthorization()
    {
        $user = User::factory()->create(['is_super_admin' => 1]);
        $this->actingAs($user);

        $requestData = [
            'name' => 'test',
            'value' => '1',
            'status' => '1',
        ];

        $response = $this->post(route('taskStatuses.store'), $requestData);
        // dd($response);
        $response->assertStatus(302);

        $this->assertDatabaseHas('task_statuses', [
            'name' => 'test',
            'value' => '1',
            'status' => '1',
            'createdBy' => $user->id,
            'updatedBy' => $user->id,
        ]);
        $response->assertRedirect(route('taskStatuses.index'))
            ->assertSessionHas('success', 'Task status created successfully.');
    }

    public function testEditStatusWithoutAuthorization()
    {
        $user = User::factory()->create(['is_super_admin' => 0]);
        $status = TaskStatus::factory()->create();

        $this->actingAs($user);

        // Assert that the redirect location is the 'dashboard' route when opening edit page
        $response = $this->get('/taskStatuses/' . $status->id . '/edit');
        $response->assertRedirect();

        // Assert that the redirect location is the 'dashboard' route
        $response->assertRedirect(route('dashboard'));

        // Assert that the session has the 'error' key with the specified message
        $response->assertSessionHas('error', 'Unauthorized Action.');

        // Assert that the redirect location is the 'dashboard' route when updating status
        $response = $this->put('/taskStatuses/' . $status->id, [
            'name' => 'test',
            'value' => '1',
        ]);
        // Assert that the redirect location is the 'dashboard' route
        $response->assertRedirect(route('dashboard'));

        // Assert that the session has the 'error' key with the specified message
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function testEditStatusWithAuthorization()
    {
        $user = User::factory()->create(['is_super_admin' => 1]);
        $status = TaskStatus::factory()->create();

        $this->actingAs($user);

        $response = $this->put('/taskStatuses/' . $status->id, [
            'name' => 'test',
            'value' => '1',
            'status' => '1',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('task_statuses', [
            'name' => 'test',
            'value' => '1',
            'status' => '1',
            'createdBy' => 1,
            'updatedBy' => $user->id,
        ]);
        $response->assertSessionHas('success', 'Task status updated successfully.');
    }

    public function testValidationForStatusCreateAndEdit()
    {
        $user = User::factory()->create(['is_super_admin' => 1]);
        $this->actingAs($user);

        $response = $this->post(route('taskStatuses.store'), [
            'name' => '',
            'value' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'value']);
        $response->assertSessionHasErrors([
            'name' => 'The name field is required.',
            'value' => 'The value field is required.',
        ]);
    }

    public function testSortRowOrder()
    {
        $user = User::factory()->create(['is_super_admin' => 1]);
        $this->actingAs($user);

        $status1 = TaskStatus::factory()->create(['listOrder' => 0, 'value' => 2]);
        $status2 = TaskStatus::factory()->create(['listOrder' => 0, 'value' => 3]);
        $status3 = TaskStatus::factory()->create(['listOrder' => 0, 'value' => 4]);

        $response = $this->post(
            '/taskStatuses/sortRowOrder',
            [
                'order' => [$status3->name, $status2->name, $status1->name],
            ],
        );

        $response->assertStatus(302);

        $response->assertSessionHas('success', 'Task status updated successfully.');

        // dd(TaskStatus::find(9));
        $this->assertDatabaseHas('task_statuses', [
            'listOrder' => 1,
            'id' => $status3->id,
        ]);

        $this->assertDatabaseHas('task_statuses', [
            'listOrder' => 2,
            'id' =>  $status2->id,
        ]);

        $this->assertDatabaseHas('task_statuses', [
            'listOrder' => 3,
            'id' =>  $status1->id,
        ]);
    }
}
