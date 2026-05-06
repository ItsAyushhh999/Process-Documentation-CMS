<?php

namespace Tests\Feature\title;

use App\Models\Title;
use App\Models\User;
use Tests\TestCase;

class ProjectTitleTest extends TestCase
{
    public function test_unauthenticated_user_can_not_render_title_index_page()
    {
        $response = $this->get('/titles');

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_only_render_title_index_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/titles');

        $response->assertStatus(200);
        // $response->assertViewIs('title.index');
    }

    public function test_display_title_info_in_index_page()
    {
        $user = User::factory()->create();
        $title = Title::create([
            'title_name' => 'Mery Johgn 12345',
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        $titles = Title::with(['createdBy', 'updatedBy'])->get();

        $this->actingAs($user);

        $response = $this->get('/titles');

        $response->assertStatus(200);
        // $response->assertViewIs('title.index');
        // $response->assertViewHas('titles', $titles);
    }

    public function test_non_superAdmin_can_not_render_title_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);

        $this->actingAs($user);

        $response = $this->get('/titles/create');

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_SuperAdmin_can_render_title_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->get('/titles/create');

        $response->assertStatus(200);
        // $response->assertViewIs('title.create');
    }

    public function test_superAdmin_can_create_title()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post('/titles', [
            'title_name' => 'Title 1',
        ]);

        $response->assertStatus(302);
        // $response->assertRedirect('/titles');
        $response->assertSessionHas('success', 'Title created successfully.');
    }

    public function test_SuperAdmin_user_can_only_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $title = Title::create([
            'title_name' => 'title 2',
        ]);

        $this->actingAs($user);

        $response = $this->put('titles/' . $title->id, [
            'title_name' => 'Title Name Change',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/titles');
        $response->assertSessionHas('success', 'Title updated successfully.');
    }

    public function test_non_SuperAdmin_user_can_not_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $title = Title::create([
            'title_name' => 'title 2',
        ]);

        $this->actingAs($user);

        $response = $this->put('titles/' . $title->id, [
            'title_name' => 'Title Name Change',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_SuperAdmin_user_can_only_render_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $title = Title::create([
            'title_name' => 'title 2',
        ]);

        $this->actingAs($user);

        $response = $this->get('titles/' . $title->id . '/edit');

        $response->assertStatus(200);
    }

    public function test_non_superAdmin_can_not_render_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $this->actingAs($user);
        $title = Title::create([
            'title_name' => 'title 2',
        ]);

        $response = $this->get('titles/' . $title->id . '/edit');

        $response->assertStatus(302);
        $response->assertRedirect('dashboard');
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_authenticated_user_can_not_render_edit_page_With_invalid_id()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $title = Title::create([
            'id' => 43,
            'title_name' => 'title 2',
        ]);

        $this->actingAs($user);

        $random_id = 878;

        $response = $this->get('titles/' . $random_id . '/edit');
        $response->assertStatus(404);
    }

    public function test_fail_for_invalid_data_when_update()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $title = Title::create([
            'title_name' => 'title 2',
        ]);

        $this->actingAs($user);

        $response = $this->put('titles/' . $title->id);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(
            ['title_name' => 'The title name field is required.'],
        );
    }

    public function test_fail_for_invalid_data_when_store()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        $response = $this->post('titles/');
        $response->assertStatus(302);
        $response->assertSessionHasErrors(
            ['title_name' => 'The title name field is required.'],
        );
    }
}
