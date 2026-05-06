<?php

namespace App\Http\Controllers;

use App\Constants\FeedConstant;
use App\Events\GenerateFeedEvent;
use App\Http\Requests\DeployRequest;
use App\Http\Services\PermissionService;
use App\Http\Traits\ApiJsonResponseTrait;
use App\Models\CodePipeLineLog;
use App\Models\Feed;
use App\Models\MergeLog;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Aws\CodePipeline\CodePipelineClient;
use Aws\Result;
use Aws\Sts\StsClient;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeployController extends Controller
{
    use ApiJsonResponseTrait;

    /**
     * Retrieves the temporary credentials for a given project.
     */
    public function getCredential($project)
    {
        $credentials = null;

        $deployAccount = $project->deploymentServer;

        if ($deployAccount) {
            $credentials = $this->initializeSts($deployAccount);
        }

        // Return the credentials (or null if no deployment server was found assuming it is in own server)
        return $credentials;
    }

    /**
     * Initializes a CodePipelineClient.
     *
     * @return CodePipelineClient
     */
    public static function initializeCodePipelineClient($credentials = null): CodePipelineClient
    {
        return new CodePipelineClient([
            'version'     => 'latest',
            'region'      => 'us-west-1',
            'credentials' => $credentials,   // Automatically handles null
        ]);
    }

    /**
     * Initializes AWS Security Token Service (STS) and assumes a role for the provided account.
     *
     * @param  mixed $account
     * @return void
     */
    public function initializeSts($account)
    {
        // Create a new STS client with the latest version and specific region
        $stsClient = new StsClient([
            'version' => 'latest',
            'region'  => 'us-west-1',
        ]);

        // Assume the role and retrieve the temporary credentials
        $credentials = $stsClient->assumeRole([
            'RoleArn'         => $account?->accountArn,
            'RoleSessionName' => $account?->role_session_name,
        ])['Credentials'];

        // Return the temporary AWS credentials as an associative array
        return [
            'key'    => $credentials['AccessKeyId'],       // Access Key ID
            'secret' => $credentials['SecretAccessKey'],   // Secret Access Key
            'token'  => $credentials['SessionToken'],      // Session Token
        ];
    }

    /**
     * Check if the user has permission to deploy based on project, user email, and deployment type.
     */
    private function _checkPermission($userEmail, $project, $type)
    {
        $defaultEmails = ['danshwaraj.c@shikhartech.com'];
        // Merge project emails with default email
        $emails = array_merge(data_get($project, $type, []), $defaultEmails);

        // Check if user's email exists in the merged email list
        return in_array($userEmail, $emails);
    }

    /**
     * Deploy a project based on the provided request information.
     * test.
     */
    public function deploy(Request $request)
    {
        // Extract stage and project names from the request
        $stage_name = $request['stage_name'];
        data_set($request, 'taskInfo', json_decode($request['taskInfo'], true));
        $project_name = ($request['taskInfo'])['project_name'];

        $projectParentId = data_get($request, 'taskInfo.sub_project');
        // Return if stage name is not provided
        if (!$stage_name) {
            return $this->failure('Stage not found.');
        }

        // Collection of projects with their deployment details
        $projects = $this->_getProjects();

        // Get current user's email
        $userEmail = auth()->user()->email;
        // Retrieve project details based on project ID
        $project = data_get($projects, $projectParentId, []);

        // Return failure if project details are not found
        if (!$project) {
            // return $this->failure('Project not available for deployment.');
            return response()->json(['pipeline_error' => 'pipeline is not set for this project.']);
        }

        // Check permissions for different deployment stages
        $productionPermission = $this->_checkPermission($userEmail, $project, 'production');
        $stagingPermission = $this->_checkPermission($userEmail, $project, 'staging');
        $developmentPermission = $this->_checkPermission($userEmail, $project, 'development');

        // Initialize default values
        $pipelineId = null;
        // $status = null;

        // Determine stage-specific details based on $stage_name
        switch (true) {
            case $stage_name == 'production' && $productionPermission:
                $pipelineId = data_get($request, 'taskInfo.production_Pipeline');
                // $status = '11'; // 11-live_upload completed;
                break;
            case $stage_name == 'stagging' && $stagingPermission:
                $pipelineId = data_get($request, 'taskInfo.staging_pipeline');
                // $status = '9'; // 9-staging_upload completed;
                break;
            case $stage_name == 'development' && $developmentPermission:
                $pipelineId = data_get($request, 'taskInfo.development_pipeline');
                break;
            default:
                return $this->failure('Not enough permission.');
                break;
        }

        // Return error if pipeline is not set
        if (!$pipelineId) {
            $stageName = ucfirst($stage_name);

            return response()->json(['pipeline_error' => "$stageName pipeline is not set for this project."]);
        }

        // // Update task status
        // if ($status) {
        //     $this->taskStatus($request, $status);
        // }
        // Return pipeline details
        $pipelineDetails = $this->getPipeLineDetails($pipelineId, $projectParentId);

        return view(
            'deploy.RequestDetail',
            [
                'pipeline_name'   => $pipelineDetails->get('pipelineName'),
                'stageStates'     => $pipelineDetails->get('stageStates'),
                'task_details'    => $project_name,
                'ParentprojectId' => $projectParentId,
                'taskId'          => data_get($request, 'taskInfo.taskId'),
                'stage_name'      => $stage_name,
            ]
        );
    }

    public function getDeploymentStage($stageName)
    {
        return match (strtolower($stageName)) {
            'development' => '0',
            'staging'     => '1',
            'production'  => '2',
        };
    }

    public function deployV2(Request $request)
    {
        $this->validate($request, [
            'stage_name' => 'required',
            'project_id' => 'required',
            'task_id'    => 'required',
        ]);

        $taskId = $request->get('task_id');
        $stageName = $request->get('stage_name');

        $stage = $this->getDeploymentStage($stageName);

        $project = Project::with(['deploymentServer' => function ($query) use ($stage) {
            $query->where('stage', $stage);
        }])->find($request->get('project_id'));

        [$pipLineName, $permissionId, $taskStatus] = $this->getPermission($project, $stageName);

        $userid = Auth::user()->id;

        $authorizedEmails = config('app.authorized_id'); // Devops users

        if (!in_array($userid, $authorizedEmails)) {
            if (!$pipLineName) {
                return response()->json(['pipeline_error' => sprintf('%s pipeline is not set for this project.', ucfirst($stageName))]);
            }
        }

        $pipelineDetails = $this->getPipeLineDetails($pipLineName, $project);

        return response()->json(
            [
                'pipeline_name'   => $pipelineDetails->get('pipelineName'),
                'stageStates'     => $pipelineDetails->get('stageStates'),
                'task_details'    => $project->name,
                'parentProjectId' => $project->sub_projects,
                'taskId'          => $taskId,
                'stage_name'      => $stageName,
                'projectId'       => $project->id,
            ]
        );
    }

    public function getPermission(Project $project, $stage_name): array
    {
        $pipLineName = null;
        $taskStatus = null;

        switch (true) {
            case $stage_name == 'production':
                $pipLineName = $project->production_Pipeline;
                $taskStatus = 10;
                break;
            case $stage_name == 'staging':
                $pipLineName = $project->staging_pipeline;
                $taskStatus = 8;
                break;
            case $stage_name == 'development':
                $pipLineName = $project->development_pipeline;
                $taskStatus = 15;
                break;
        }

        $permissionId = Permission::where('name', strtoupper($stage_name))->first()?->id;

        return [$pipLineName, $permissionId, $taskStatus];
    }

    public function getPipeLineDetails($pipelineName, $project): ?Result
    {
        $credentials = $this->getCredential($project);

        $codePipeline = $this->initializeCodePipelineClient($credentials);

        return $codePipeline->getPipelineState([
            'name' => $pipelineName,
        ]);
    }

    public function taskStatus($request, $status)
    {
        $task_id = $request['taskInfo']['taskId'];
        $task = Task::find($task_id);

        if ($task) {
            $task->status = $status;
            $task->save();
        }
    }

    public function deployResult(DeployRequest $request, PermissionService $permissionService)
    {
        $deploy = $request->deploy;
        $deploy_token = $request->deploy_token;
        $manualApproval = $deploy === 'Approved' ? 'Approved' : 'Rejected';

        // $project = Project::find($request->get('project_id'));
        $stage = $this->getDeploymentStage($request->get('stage_name'));

        $project = Project::with(['deploymentServer' => function ($query) use ($stage) {
            $query->where('stage', $stage);
        }])->find($request->get('project_id'));

        $credentials = $this->getCredential($project);

        $codePipeLine = $this->initializeCodePipelineClient($credentials);

        $taskId = $request->get('deploy_taskId');
        $task = Task::find($taskId);
        $collaborators = $task->collaborators;
        $assignee = $collaborators->where('flag', '0');
        $reviewer = $collaborators->where('flag', '1');

        [$pipLineName, $permissionId, $taskStatus] = $this->getPermission($project, $request->get('stage_name'));

        $userid = Auth::user()->id;

        $authorizedEmails = config('app.authorized_id'); // Devops users

        if (!in_array($userid, $authorizedEmails)) {
            if (!$permissionId || !$permissionService->isPermitted($project->id, $permissionId) || !$collaborators->contains('collaborator', Auth::user()->id) || ($assignee->contains('collaborator', Auth::user()->id) && $reviewer->contains('collaborator', Auth::user()->id))) {
                return $this->failure('Not enough permission.');
            }
        }
        if ($task->status != $taskStatus) {
            return $this->failure(sprintf('Task status must be  on %s ready to upload ', $request->get('stage_name')));
        }

        try {
            $codePipelineResponse = $codePipeLine->putApprovalResult([
                'actionName'    => $request['deploy_action_name'],
                'pipelineName'  => $request['deploy_pipeline_name'],
                'result'        => [
                    'status'    => $deploy,
                    'summary'   => 'Manual approval ' . $manualApproval,
                ],
                'stageName'     => $request['deploy_stage_name'],
                'token'         => $deploy_token,
            ]);

            if ($codePipelineResponse) {
                CodePipeLineLog::create([
                    'created_by'   => auth()->user()->name,
                    'updated_by'   => auth()->user()->id,
                    'task_id'      => $request['deploy_taskId'],
                    'project_name' => $request['deploy_projectName'],
                    'project_id'   => $request['project_id'],
                    'deploy'       => $request['deploy'],
                    'pull_request' => $request->pull_request ?? null,
                    //                    'commit'       => $request->commit ?? null,
                    'summary'      => $request->summary ?? null,
                    'stage_name'    => $request->get('stage_name'),
                ]);

                $task = Task::find($request->get('deploy_taskId'));

                $feed = Feed::create([
                    'created_by'    => auth()->user()->id,
                    'updated_by'    => auth()->user()->id,
                    'task_id'       => $request->get('deploy_taskId'),
                    'project_id'    => $request['project_id'],
                    'type'          => FeedConstant::FEED_TYPE_DEPLOYMENT,
                    'status'        => FeedConstant::FEED_STATUS_PROCESS,
                    'source'        => FeedConstant::FEED_SOURCE_AWS,
                    'description'   => 'Manual approval ' . $manualApproval,
                    'title'         => sprintf('%s started for deploying %s to the %s environment of the %s project.', auth()->user()->name, $task->title, $request->get('stage_name'), $task->project->name),
                ]);

                event(new GenerateFeedEvent($feed->toArray()));
            }
        } catch (Exception $e) {
            // return back()->with('error', $e->getMessage());
            return $this->failure($e->getMessage());
        }

        return $this->success('Deployed ' . $deploy . ' Successfully');
    }

    public function deployLogList(Request $request)
    {
        $project_id = $request->input('project_id');
        $task_id = $request->input('task_id');

        if (!$project_id || !$task_id) 
        {
            return response()->json([]);
        }

        $project = Project::find($project_id);

        if (!$project) {
            return response()->json(['error' => 'Project not found.']);
        }
        $codePipeLineLogs = CodePipeLineLog::where('task_id', $task_id);
        if (!$project->sub_projects) {
            $projectId = $project->subprojects->pluck('id')->toArray();
            $codePipeLineLogs->whereIn('project_id', $projectId);
        } else {
            $codePipeLineLogs->where('project_id', $project->id);
        }
        $codePipeLineLogs = $codePipeLineLogs->get();

        return $codePipeLineLogs;
    }

    public function pullrequest(Request $request)
    {
        $name = $request->repository_name;

        if (!$name) {
            return $this->failure('Repository name is required.');
        }

        $owner = config('github.owner');
        $accessToken = config('github.accessToken');

        $checkGithubRepo = Project::where('repository_name', $name)->exists();

        if (!$checkGithubRepo) {
            return $this->failure('Repo does not exist.');
        }

        $assigneeIds = $request->assigneesIds;
        $reviewerIds = $request->reviewersIds;

        $users = User::whereIn('id', $assigneeIds)->pluck('github_username')->toArray();
        $user = Auth::user();

        try {
            $client = new Client([
                'base_uri' => 'https://api.github.com/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/vnd.github.v3+json',
                ],
            ]);

            $response = $client->get("repos/$owner/$name/pulls?state=open");
            $content = $response->getBody()->getContents();
            $pullRequests = json_decode($content, true);

            $data = [];

            foreach ($pullRequests as $pr) {
                if (($pr['user']['login'] === $user->github_username && in_array($pr['user']['login'], $users)) ||
                    (in_array($user->id, $reviewerIds) && in_array($pr['user']['login'], $users))
                ) {
                    $data[] = [
                        'number'   => data_get($pr, 'number'),
                        'title'    => data_get($pr, 'title'),
                        'body'     => data_get($pr, 'body'),
                        'username' => User::where('github_username', $pr['user']['login'])->first()->name ?? $pr['user']['login'],
                        'source'   => $pr['head']['label'],
                        'target'   => $pr['base']['label'],
                    ];
                }
            }
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }

        return response()->json(['data' => $data]);
    }

    public function mergePullRequest(Request $request, PermissionService $permissionService)
    {
        $owner = config('github.owner');
        $accessToken = config('github.accessToken');
        $repositoryName = $request->repository_name;
        $pullRequest = json_decode($request->pull_request, true);
        $prNo = $pullRequest['number'];
        $task = Task::find($request->taskId);
        $user = Auth::user();
        $collaborators = $task->collaborators;
        $project = Project::where('repository_name', $repositoryName)->first();
        $assignee = $collaborators->where('flag', '0');
        $reviewer = $collaborators->where('flag', '1');

        if (!$project) {
            return back()->with('error', 'Project not found.');
        }

        $client = new Client([
            'base_uri' => 'https://api.github.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);

        $response = $client->get("repos/$owner/$repositoryName/pulls/$prNo");
        $getContents = $response->getBody()->getContents();
        $repository = json_decode($getContents);

        if (!$repository) {
            return back()->with('error', 'Please check repo name.');
        }

        $repository->base->ref == 'main' ? $target = 'production' : $target = $repository->base->ref;
        [$pipLineName, $permissionId, $taskStatus] = $this->getPermission($project, $target);
        $userid = Auth::user()->id;

        $authorizedEmails = config('app.authorized_id');

        if (!in_array($userid, $authorizedEmails)) {
            if (!$permissionId || !$permissionService->isPermitted($project->id, $permissionId) || !$collaborators->contains('collaborator', $user->id) || $task->status != $taskStatus || ($assignee->contains('collaborator', $user->id) && $reviewer->contains('collaborator', $user->id))) {
                // return $this->failure('Not enough permission.');
                return back()->with('error', 'Not enough permission.');
            }
        }
        try {
            $response = $client->put("repos/$owner/$repositoryName/pulls/$prNo/merge", [
                'json' => [
                    'commit_title' => 'Merged from TDMS by ' . auth()->user()->name,
                    'commit_message' => "PR# $prNo merged by " . auth()->user()->name,
                    'merge_method' => 'merge',
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                MergeLog::create([
                    'taskId'    => $task->id,
                    'projectId' => $task->project_id,
                    'pr_no'     => $prNo,
                    'source'    => $pullRequest['source'],
                    'target'    => $pullRequest['target'],
                    'merge_by'  => $user->id,
                ]);

                return back()->with('success', 'Merged successfully for repositories: ' . $repositoryName);
            }

            return back()->with('error', 'Something went wrong!.');
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    public function taskDeployPermission(Request $request, $projectId, PermissionService $service)
    {
        $permissions = $service->permissionNameByProject($projectId);

        return response()->json([
            'permissions' => $permissions,
        ]);
    }
}
