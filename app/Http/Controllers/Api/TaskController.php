<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\TaskService;
use App\Http\Traits\ApiTrait;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCollaborator;
use App\Models\TaskComment;
use App\Models\TaskType;
use App\Models\User;
use App\Services\FileHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    use ApiTrait;

    private $taskService;
    private $fileHandlerService;

    public function __construct(TaskService $taskServ, FileHandler $fileHandlerSer)
    {
        $this->taskService = $taskServ;
        $this->fileHandlerService = $fileHandlerSer;
    }

    public function taskList(Request $request)
    {
        $projectId = $request->projectId;
        if (!$projectId) {
            return $this->failure('No project found');
        }

        $statusFlags = [
            0 => 'assigned',
            1 => 'In Progress',
            2 => 'Assigned for review',
            3 => 'reviewed',
            4 => 'Completed',
            5 => 'Closed',
            6 => 'Created',
            7 => 'Staging- Ready to upload',
            8 => 'Staging- Upload Completed',
            9 => 'Live- Ready to Upload',
            10 => 'Live- Upload Completed',
        ];

        $tasks = Task::select('id', 'title', 'description', 'status')
            ->where('project_id', $projectId)
            ->get()
            ->map(function ($task) use ($statusFlags) {
                $task->status = $statusFlags[$task->status];

                return $task;
            });

        if ($tasks->isEmpty()) {
            return $this->failure('No task found.');
        }

        if ($tasks) {
            return $this->success('Tasks fetched successfully', $tasks);
        } else {
            return $this->failure('No task found');
        }
    }

    public function taskDetails($task)
    {
        $tasks = Task::selectRaw("tasks.id, tasks.title, tasks.description,
        CASE
            WHEN tasks.priority = 1 THEN 'Normal'
            WHEN tasks.priority = 2 THEN 'High'
            WHEN tasks.priority = 3 THEN 'Urgent'
            ELSE 'Unknown'
        END AS task_priority,
        CASE
            WHEN tasks.status = 1 THEN 'assigned'
            WHEN tasks.status = 2 THEN 'In Progress'
            WHEN tasks.status = 3 THEN 'Assigned for review'
            WHEN tasks.status = 4 THEN 'reviewed'
            WHEN tasks.status = 5 THEN 'Completed'
            WHEN tasks.status = 6 THEN 'Closed'
            WHEN tasks.status = 7 THEN 'Created'
            WHEN tasks.status = 8 THEN 'Staging- Ready to upload'
            WHEN tasks.status = 9 THEN 'Staging- Upload Completed'
            WHEN tasks.status = 10 THEN 'Live- Ready to Upload'
            WHEN tasks.status = 11 THEN 'Live- Upload Completed'
            ELSE 'Unknown'
        END AS task_status,
        tasks.deadline,
        (SELECT GROUP_CONCAT(comments) FROM task_comments WHERE task_comments.taskId = tasks.id ORDER BY created_at DESC) AS comments,
        GROUP_CONCAT(DISTINCT IF(task_collaborators.flag = '0', users.name, NULL)) AS assignees,
        GROUP_CONCAT(DISTINCT IF(task_collaborators.flag = '1', users.name, NULL)) AS reviewers,
        GROUP_CONCAT(DISTINCT taskTypes.type) AS task_types,
        projects.name AS project_name")
            ->leftJoin('task_collaborators', 'tasks.id', '=', 'task_collaborators.taskId')
            ->leftJoin('users', 'task_collaborators.collaborator', '=', 'users.id')
            ->leftJoin('projects', 'tasks.project_id', '=', 'projects.id')
            ->leftJoin('task_taskTypes_pivot', 'tasks.id', '=', 'task_taskTypes_pivot.task_id')
            ->leftJoin('taskTypes', 'task_taskTypes_pivot.taskTypes_id', '=', 'taskTypes.id')
            ->where('tasks.id', $task)
            ->groupBy('tasks.id', 'tasks.title', 'tasks.description', 'projects.name')
            ->get();

        if ($tasks->isEmpty()) {
            return $this->failure('No data found.');
        }

        if ($tasks) {
            return $this->success('Tasks fetched successfully', $tasks);
        } else {
            return $this->failure('Task not found.');
        }
    }

    public function createTask(Request $request)
    {

        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'priority' => 'required',
            'deadline' => 'required',
            'project_id' => 'required|exists:projects,id',
            'reviewers' => 'required',
            'type' => 'required',
            'attachments'   => 'nullable|array|max:10',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,pdf,csv,xls,xlsx,txt,docx',
        ];

        // // Run the validation
        $validator = Validator::make($request->all(), $rules);

        // // Return an error response if the validation fails
        if ($validator->fails()) {
            return $this->failure($validator->errors()->first());
        }

        $createTask = $this->taskService->createTask($request);

        if ($createTask['status'] == 'success') {
            $task = $createTask['result'];
            //Calling TaskStore Class From TaskService for stroing task
            if ($request->has('attachments') && !empty($request->attachments)) {
                //Calling file_store class from FileHandleService for storing
                $images = $this->fileHandlerService->file_store($request->attachments);
                foreach ($images as $image) {
                    Attachment::create([
                        'task_id' => $task->id,
                        'name' => $image, // Save each image name individually
                        'flag' => '0',
                    ]);
                }
            }

            if ($request->has('uploadedAttachments') && !empty($request->uploadedAttachments)) {
                //Calling file_store class from FileHandleService for storing
                $images = $this->fileHandlerService->file_store($request->uploadedAttachments);
                foreach ($images as $image) {
                    Attachment::create([
                        'task_id' => $task->id,
                        'name' => $image, // Save each image name individually
                        'flag' => '0',
                    ]);
                }
            }

            if ($task) {

                $this->taskService->slackEmailAndMessage($task);

                return $this->success('Tasks created successfully', $task);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $createTask['message'],
            ]);
        }
    }

    public function updateTask(Request $request, $task)
    {
        $user = $request->user()->id;

        $task = Task::find($task);

        $rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'priority' => 'required',
            'status' => 'required',
            'deadline' => 'date_format:Y-m-d H:i:s',
            'project_id' => 'required|exists:projects,id',
            'type' => 'required',
            'reviewers' => 'required',
        ];

        // Run the validation
        $validator = Validator::make($request->all(), $rules);

        // Return an error response if the validation fails
        if ($validator->fails()) {
            return $this->failure($validator->errors()->all());
        }

        if (!$task) {
            return $this->failure('Task not found.');
        }

        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->priority = $request->input('priority');
        $task->status = $request->input('status');
        $task->deadline = $request->input('deadline');
        $task->project_id = $request->input('project_id');
        $task->updatedBy = $user;

        $task->TaskType()->sync($request->input('type'));

        $assignees = $request->input('assignees');
        $reviewers = $request->input('reviewers');

        if (is_string($assignees)) {
            $assignees = explode(',', $assignees);
        }

        if (is_string($reviewers)) {
            $reviewers = explode(',', $reviewers);
        }

        if (is_array($assignees)) {
            TaskCollaborator::where('taskId', $task->id)->where('flag', '0')->delete();
            foreach ($assignees as $assignee_id) {
                $assignee = User::find($assignee_id);
                if ($assignee) {
                    TaskCollaborator::create([
                        'taskId' => $task->id,
                        'collaborator' => $assignee->id,
                        'flag' => '0',
                        'createdBy' => $user,
                    ]);
                }
            }
        }

        if (is_array($reviewers)) {
            TaskCollaborator::where('taskId', $task->id)->where('flag', '1')->delete();
            foreach ($reviewers as $reviewer_id) {
                $reviewer = User::find($reviewer_id);
                if ($reviewer) {
                    TaskCollaborator::create([
                        'taskId' => $task->id,
                        'collaborator' => $reviewer->id,
                        'flag' => '1',
                        'createdBy' => $user,
                    ]);
                }
            }
        }

        $task->save();

        return $this->success('Task updated successfully', $task);
    }

    public function taskComment(Request $request, $task)
    {
        if (!$task) {
            return $this->failure('Task not found.');
        }

        $user = $request->user()->id;

        $rules = [
            'comments' => 'required',
        ];

        // Run the validation
        $validator = Validator::make($request->all(), $rules);

        // Return an error response if the validation fails
        if ($validator->fails()) {
            return $this->failure($validator->errors()->all());
        }

        $comment = TaskComment::create([
            'taskId' => $task,
            'comments' => $request->input('comments'),
            'createdBy'   => $user,
        ]);

        if ($comment) {
            return $this->success('comment created successfully', $comment);
        }
    }

    public function resource()
    {
        $taskTypes = TaskType::select('id', 'type')->latest()->get();
        $projects = Project::with(['subprojects' => function ($q) {
            $q->select('name', 'id', 'sub_projects');
        }])->select('id', 'name')->where('sub_projects', '0')->latest()->get();
        $users = User::select('id', 'name')->latest()->get();
        $tdms = [];

        $tdms['task_types'] = $taskTypes;
        $tdms['projects'] = $projects;
        $tdms['users'] = $users;

        return response()->json([
            'message' => 'tdms resource fetch successfully.',
            'result' => $tdms,
        ]);
    }
}
