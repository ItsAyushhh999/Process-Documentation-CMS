<?php

namespace Tests\Feature\user;

use App\Models\Department;
use App\Models\Title;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test case for user's edit page.
     *
     * @return void
     */
    public function test_authenticated_user_can_only_render_user_edit_page()
    {
        $department = Department::create([
            'department_name'  => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'is_super_admin'   => '1',
            'department_id' => $department->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/users/' . $user->id . '/edit');

        $response->assertStatus(200);
        $response->assertViewIs('user.user_department_title');
    }

    public function test_authenticated_user_can_only_render_user_edit_page_without_enough_privilege()
    {
        $user = User::factory()->create([
            'is_super_admin'   => '1',
        ]);

        $this->actingAs($user);

        $userOne = User::factory()->create();

        $response = $this->get('/users/' . $userOne->id . '/edit');
        $response->assertStatus(302);
        $response->assertRedirect();
        $response->assertSessionHas('error', 'Not enough privileges.');
    }

    public function test_unauthenticated_user_can_not_render_user_edit_page()
    {
        $department = Department::create([
            'department_name'  => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'is_super_admin'   => '1',
            'department_id' => $department->id,
        ]);

        $response = $this->get('/users/' . $user->id . '/edit');

        $response->assertStatus(302);
    }

    public function test_update_user()
    {
        $department = Department::create([
            'department_name'  => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'is_super_admin' => 1,
            'department_id'  => $department->id,
        ]);

        $title = Title::create([
            'title_name'   => 'Title one',
        ]);

        $this->actingAs($user);

        $requestData = [
            'department' => "Project Administration,$department->id",
            'title'  => $title->id,
            'phone'  => '9878787876',
            'slack_username' => 'User one',
            'status'         => '0', // make it inactive
            'feed_scope'     => '1',
        ];

        $response = $this->put('users/' . $user->id, $requestData);
        $response->assertSessionHas('success', 'User updated successfully.');
        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_update_other_user()
    {
        $department = Department::create([
            'department_name'  => 'Project Administration',
        ]);

        $user = User::factory()->create([
            'is_super_admin' => 1,
            'department_id'  => $department->id,
        ]);

        $userOne = User::factory()->create();

        $title = Title::create([
            'title_name'   => 'Title one',
        ]);

        $this->actingAs($user);

        $requestData = [
            'department' => "Project Administration,$department->id",
            'title'  => $title->id,
            'phone'  => '9878787876',
            'slack_username' => 'User one',
            'status'         => '0', // make it inactive
            'feed_scope'     => '1',
        ];

        $response = $this->put('users/' . $userOne->id, $requestData);
        $response->assertSessionHas('success', 'User updated successfully.');
        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_authenticated_user_can_render_user_index_page()
    {
        $department = Department::create([
            'department_name'    => 'Department One',
        ]);

        $user = User::factory()->create([
            'slack_username' => 'Mery John',
            'phone'          => '9898989897',
            'department_id'  => $department->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/users');

        $response->assertStatus(200);
        // $response->assertViewIs('user.index');

        // $usersInView = $response->original->getData()['users'];

        // $this->assertTrue($usersInView->contains('name', $user->name));
        // $this->assertTrue($usersInView->contains('phone', $user->phone));
        // $this->assertTrue($usersInView->contains('slack_username', $user->slack_username));

        //       $response->assertViewHas('users', function ($users) use($user) {
        //           return $users->contains('name', $user->name);
        //       });
    }

    public function test_unauthenticated_user_can_not_render_user_index_page()
    {
        $response = $this->get('/users');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }
}
