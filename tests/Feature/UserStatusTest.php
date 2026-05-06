<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Title;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatusTest extends TestCase
{
    use RefreshDatabase;

    public function testChangeUserStatus()
    {
        $department = Department::create([
            'department_name' => 'test',
        ]);

        $title = Title::create([
            'title_name' => 'test',
        ]);

        $user = User::factory()->create([
         'status' => '1',
         'is_super_admin' => '1',
         'department_id' => $department->id,
         'title_id' => $title->id,
         ]); // active user

        $requestData = [
            'department'     =>"$department->department_name,$department->id",
            'title'          => $title->id,
            'phone'          => 1234567890,
            'slack_username' => 'test',
            'status'         => '0', // make it inactive
        ];

        $this->actingAs($user);

        $response = $this->put("/users/{$user->id}", $requestData);

        $response->assertStatus(302); // redirect
        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => '1']);

    }

    public function testUserStatusFilter()
    {
        $usersActive = User::factory()->count(5)->create([
            'status' => '1',
        ]);

        $usersInactive = User::factory()->count(4)->create([
            'status' => '0',
        ]);

        $this->actingAs(User::factory()->create());

        // get all users
        $response = $this->get('/users?status=0');
        // $response->assertViewIs('user.index');
        foreach ($usersInactive as $user) {
            $response->assertSee($user->name);
        }

        foreach ($usersActive as $user) {
            $response->assertDontSee($user->name);
        }

        // $viewUsers = $response->viewData('users');
        // $this->assertEquals(4, count($viewUsers)); // only inactive users

        // get only active users
        $response = $this->get('/users?status=1');
        // $response->assertViewIs('user.index');

        foreach ($usersActive as $user) {
            $response->assertSee($user->name);
        }

        foreach ($usersInactive as $user) {
            $response->assertDontSee($user->name);
        }

        // $viewUsers = $response->viewData('users');
        // $this->assertEquals(6, count($viewUsers)); // only active users
    }
}
