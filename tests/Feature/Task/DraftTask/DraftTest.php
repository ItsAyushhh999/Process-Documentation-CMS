<?php

namespace Task\DraftTask;

use App\Models\User;
use Tests\TestCase;

class DraftTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_authenticated_user_can_only_view_draft_task()
    {
        $user = User::factory()->create([
            'is_super_admin'    => 1,
        ]);
        $this->actingAs($user);

        $response = $this->get('/tasks/draft/index');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_can_not_view_draft_task()
    {

        $response = $this->get('/tasks/draft/index');

        $response->assertStatus(302);
    }
}
