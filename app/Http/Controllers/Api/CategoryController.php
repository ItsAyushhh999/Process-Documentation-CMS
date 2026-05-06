<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiTrait;

    public function categoryList(Request $request)
    {
        $projectId = $request->projectId;
        if (!$projectId) {
            return $this->failure('No project found');
        }

        $categories = Category::select('categories.id', 'categories.name', 'categories.description')
        ->join('project_categories', 'project_categories.category_id', '=', 'categories.id')
        ->where('project_id', $projectId)
        ->get();

        if ($categories->isEmpty()) {
            return $this->failure('No category found.');
        }

        return $this->success('Categories fetched successfully', $categories);
    }
}
