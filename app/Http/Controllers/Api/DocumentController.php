<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Http\Resources\ProjectResource;
use App\Http\Traits\ApiTrait;
use App\Models\Document;
use App\Models\Project;

class DocumentController extends Controller
{
    use ApiTrait;

    /**
     * API to list all published documents for a project.
     */
    public function documentList($id)
    {
        $projectId = Project::where('sub_projects', '=', 0)->find($id);

        if (!$projectId) {
            return $this->failure('Project not found', 404);
        }

        $projects = Project::select('id', 'name', 'id as projectId')
        ->with([
            'documents' => function ($query) {
                $query->select('name', 'id', 'project_id')->where('isPublished', 1)->orderBy('position', 'ASC')->with('category:id,name');
            },
            'subprojects' => function ($query) {
                $query->select('id', 'name', 'sub_projects', 'sub_projects as parentId', 'id as subProjectId')->with([
                    'documents' => function ($subProjectQuery) {
                        $subProjectQuery->select('name', 'id', 'project_id')->where('isPublished', 1)->orderBy('position', 'ASC')->with('category:id,name');
                    },
                ]);
            },
        ])
        ->where('id', $id)
        ->first();

        if (!$projects) {
            return $this->failure('Document not found');
        }

        // return $projects;
        return $this->success('Documents fetched successfully.', new ProjectResource($projects));
    }

    /**
     * API to get details of the published document.
     */
    public function documentDetails($documentId)
    {

        $document = Document::where('isPublished', 1)->find($documentId);

        if (!$document) {
            return $this->failure('Document not found');
        }

        $documentDetail = $document::with(['project:id,name', 'category:id,name'])
                            ->where('isPublished', 1)
                            ->where('id', $documentId)
                            ->select('id', 'name', 'description', 'project_id', 'id as documentId', 'updated_at', 'updatedBy')
                            ->updater()
                            ->first();

        return $this->success('Document detail fetched successfully.', new DocumentResource($documentDetail));
    }
}
