<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    //    use RefreshDatabase;
    public function test_authenticated_user_can_only_render_categories_index_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => '1',
        ]);

        $this->actingAs($user);

        $response = $this->get('/categories');

        // $response->assertViewIs('category.index');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_can_not_render_categories_index_page()
    {
        $response = $this->get('/categories');

        $response->assertStatus(302);

    }

    public function test_authenticated_superAdmin_user_can_only_render_categories_create_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => '1',
        ]);
        $this->actingAs($user);

        $response = $this->get('/categories/create');
        $response->assertStatus(200);
        // $response->assertViewIs('category.create');
    }

    // public function test_user_withOut_superAdmin_can_not_render_categories_created_page()
    // {
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     $response = $this->get('/categories/create');

    //     $response->assertStatus(200);
    // }

    public function test_authenticated_user_can_only_render_category_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);
        $category = Category::create([
            'name' => 'Category One',
            'description'   => 'Category Description',
            'logo' => 'LogoOne.jpg',
        ]);

        $this->actingAs($user);

        $response = $this->get('categories/' . $category->id . '/edit');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_without_SuperAdmin_can_not_render_category_edit_page()
    {
        $user = User::factory()->create([
            'is_super_admin' => 0,
        ]);
        $category = Category::create([
            'name' => 'Category One',
            'description'   => 'Category Description',
            'logo' => 'LogoOne.jpg',
        ]);

        $this->actingAs($user);

        $response = $this->get('categories/' . $category->id . '/edit');

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Unauthorized Action.');
    }

    public function test_store_categories_without_Logo()
    {
        $user = User::factory()->create([
            'is_super_admin'  => 1,
        ]);

        $this->actingAs($user);

        $projects = Project::factory()->count(2)->create();

        $requestedData = [
            'name' => 'Category one',
            'description' => 'Category one is important',
            'projects' => [$projects->first()->id, $projects->last()->id],
        ];

        $response = $this->post('/categories', [
            'name'          => $requestedData['name'],
            'description'   => $requestedData['description'],
            'projects'      => $requestedData['projects'],
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
            'status'        => '1',
        ]);
        $response->assertStatus(302);
        // $response->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => $requestedData['name'],
            'description'   => $requestedData['description'],
        ]);

        $category = Category::latest()->first();

        foreach ($projects as $project) {
            $this->assertDatabaseHas('project_categories', [
                'project_id'    => $project->id,
                'category_id'   => $category->id,
            ]);
        }

    }

    public function test_store_category_with_logo()
    {
        $user = User::factory()->create([
            'is_super_admin'  => 1,
        ]);
        $projects = Project::factory()->count(2)->create();

        $this->actingAs($user);

        $requestedData = [
            'name' => 'Category one',
            'description' => 'Category one is important',
            'projects' => [$projects->first()->id, $projects->last()->id],
        ];

        $storage = Storage::fake('images');
        $file = UploadedFile::fake()->image('logo.png');

        $response = $this->post('/categories', [
            'name'          => $requestedData['name'],
            'description'   => $requestedData['description'],
            'projects'      => $requestedData['projects'],
            'logo'          => $file,
            'status'        =>'1',
            'created_by'    => $user->id,
            'updated_by'    => $user->id,
        ]);

        $response->assertStatus(302);
        // $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => $requestedData['name'],
            'description'   => $requestedData['description'],
        ]);

        $category = Category::latest()->first();
        foreach ($projects as $project) {
            $this->assertDatabaseHas('project_categories', [
                'project_id'    => $project->id,
                'category_id'   => $category->id,
            ]);
        }
    }

    public function test_category_update_withOut_logo_upload()
    {
        $user = User::factory()->create([
            'is_super_admin'    => 1,
        ]);

        $this->actingAs($user);

        $category = Category::factory()->create();
        $project = Project::factory()->create();

        $storage = Storage::fake('logo');
        $file = UploadedFile::fake()->image('logo1.jpg');

        $response = $this->put('categories/' . $category->id, [
            'name'  => 'Updated category one',
            'description'  => 'Updated category description',
            'projects' => [$project->id],
            'logo'  => $file,
            'status' => '1',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated category one',
            'description' => 'Updated category description',
        ]);
    }

    // public function test_order_update()
    // {
    //     $user = User::factory()->create([
    //         'is_super_admin' => 1,
    //     ]);

    //     $this->actingAs($user);

    //     $category = Category::factory()->count(5)->create();

    //     $data = [
    //         ['id' => $category[0]->id,'position' => 2],
    //         ['id' => $category[1]->id,'position' => 5],
    //         ['id' => $category[2]->id,'position' => 4],
    //         ['id' => $category[3]->id,'position' => 3],
    //         ['id' => $category[4]->id,'position' => 2],
    //     ];

    //     $response = $this->put('/categories/position/update',['body'=> $data]);
    //     $response->assertStatus(200);
    // }

    public function test_fetching_active_and_inactive_categories_when_toggle_is_checked()
    {
        // Create a super admin user with the role 'SUPER ADMIN'
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        // Create active and inactive categories
        $inactiveCategory = Category::factory()->create(['status' => '0']); // Inactive category
        $activeCategory = Category::factory()->create(['status' => '1']);   // Active category

        // should only show active categories
        $response = $this->get('/categories?showAll=0');
        $response->assertStatus(200);
        $response->assertSee($activeCategory->name);
        $response->assertSee($inactiveCategory->name);

        // should show both active and inactive categories
        $response = $this->get('/categories?showAll=1');
        $response->assertStatus(200);
        $response->assertSee($activeCategory->name);
        $response->assertSee($inactiveCategory->name);
    }

    public function test_superAdmin_can_change_status_of_category()
    {
        // Create a super admin user with the role 'SUPER ADMIN'
        $user = User::factory()->create([
            'is_super_admin' => 1,
        ]);

        $this->actingAs($user);

        // Create a category instance
        $category = Category::factory()->create(['status' => '1']);

        $this->assertEquals($category->status, '1');

        // Attempt to change the status of the category
        $response = $this->put(route('categories.status.update', ['category' => $category->id]));

        // Assert that the session has a 'success' message indicating successful status update
        $response->assertSessionHas('success', 'Category status updated successfully.');

        // Assert that the category status is changed to '0'
        $this->assertEquals(Category::where('id', $category->id)->value('status'), '0');
    }
}
