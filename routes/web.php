<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CronJobController;
use App\Http\Controllers\DailyFeedController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\DevopsFeedsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TasksCommentController;
use App\Http\Controllers\TaskStatusController;
use App\Http\Controllers\TaskTypeController;
use App\Http\Controllers\TaskWatchListController;
use App\Http\Controllers\TitleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionController;
use App\Http\Controllers\Webhooks\GithubPrWebhooksController;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
//use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome')->middleware('guest')->name('login');

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirect'])->name('googleAuth');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'callBackGoogle'])->name('callBack');

Route::post('logout', function () {
    
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
    
})->name('logout');


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/devopsFeeds', [DevopsFeedsController::class, 'index'])->name('devopsFeeds.index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/generateToken', [AuthController::class, 'generateToken'])->middleware('admin');
    //profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/subProjects', [ProjectController::class, 'subProject'])->name('subProjects');
    Route::get('/project', [ProjectController::class, 'index'])->name('index');

    Route::get('/projects/{id}/subProjects', [ProjectController::class, 'viewSubProjects'])->name('view.sub-projects');
    Route::resource('projects', ProjectController::class)->except(['show', 'destroy']);
    Route::resource('categories', CategoryController::class)->except(['show', 'destroy']);
    Route::put('/categories/{category}/change-status', [CategoryController::class, 'changeStatus'])->name('categories.status.update');
    Route::resource('document', DocumentController::class)->except(['show', 'destroy']);
    Route::put('project/document/order', [DocumentController::class, 'sortDocument'])->name('document.order'); //Sorting Project Document
    Route::put('project/category/order', [ProjectController::class, 'sortCategory'])->name('project.category.order'); //Sorting Project Category
    Route::get('project/{project}/document', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('document/create/{id}', [DocumentController::class, 'create'])->name('document.create');
    Route::post('/document/status', [DocumentController::class, 'updateStatus'])->name('document.status');

    Route::put('/document/position/update', [DocumentController::class, 'updateOrder'])->name('documents.updateOrder');
    Route::get('project/{project}/document/{document?}', [DocumentController::class, 'projectDocuments'])->name('documents.show');
    Route::get('document/delete/{id}', [DocumentController::class, 'destroy'])->name('document.delete');

    Route::get('users/{user}/tasks', [TaskController::class, 'userTaskList'])->name('users.userTaskList');
    Route::resource('tasks', TaskController::class)->except(['destroy', 'show']);
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/{taskId}/editV2', [TaskController::class, 'editV2']);
    Route::get('tasks/{taskId}/editV3', [TaskController::class, 'editV3']);
    Route::get('tasks/{taskId}/comments', [TaskController::class, 'comments']);
    Route::post('tasks/{task}/updateV2', [TaskController::class, 'updateV2'])->name('updateV2');

    Route::get('/tasks/deadline', [TaskController::class, 'uncompletedTask'])->name('tasks.taskList');

    // Route::put('/tasks/{id}/updateStatus',[TaskController::class,'updateTaskStatus'])->name('task.statusUpdate');
    Route::get('/tasks/draft/index', [TaskController::class, 'index'])->name('task.draft');
    Route::get('/tasks/deploy/permission/{projectId}', [DeployController::class, 'taskDeployPermission'])->name('task.deploy.permission');
    // Route::get('Attachment/{id}',[AttachmentController::class,'index'])->name('attachment.index');
    Route::get('Attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
    Route::get('/filter', [DocumentController::class, 'filter'])->name('documents.filter');

    Route::resource('users', UserController::class)->except(['create', 'show', 'destroy']);
    Route::prefix('tasks/comments')->group([function () {
        Route::post('/create', [TasksCommentController::class, 'store'])->name('tasks.comments.store');
        Route::post('/createV2', [TasksCommentController::class, 'storeV2'])->name('tasks.comments.storeV2');
        Route::post('/{id}/pinComment', [TasksCommentController::class, 'pinComment'])->name('tasks.pinComment');
        Route::post('/{id}/checked', [TasksCommentController::class, 'commentChecked'])->name('tasks.checkedComment');
    }]);
    Route::get('/stagingUploadedTasks', [TaskController::class, 'getStagingUploadedTasks'])->name('StagingUploadedTasks');

    Route::resource('departments', DepartmentController::class)->except(['show', 'destroy']);
    Route::resource('titles', TitleController::class)->except(['show', 'destroy']);

    Route::resource('taskTypes', TaskTypeController::class)->except(['show', 'destroy']);

    Route::get('search', [SearchController::class, 'index'])->name('search.list');

    Route::post('/deploy/request', [DeployController::class, 'deployV2'])->name('deploy');
    Route::post('/deploy', [DeployController::class, 'deployResult'])->name('deploy.result');
    Route::get('/deploy/log_list', [DeployController::class, 'deployLogList'])->name('deploy.log.list');
    Route::get('/pipelineList', [DeployController::class, 'getPipelineList'])->name('deploy.pipeline.list');

    Route::prefix('permissions')->group(function () {
        Route::get('/{user}/edit', [UserPermissionController::class, 'index'])->name('permissions.edit');
        Route::patch('/{user}/update', [UserPermissionController::class, 'update'])->name('permissions.update');
    });

    // task status routes
    Route::post('/taskStatuses/sortRowOrder', [TaskStatusController::class, 'sortRowOrder'])->name('taskStatuses.sortRowOrder');
    Route::resource('taskStatuses', TaskStatusController::class)->except(['show', 'destroy']);
    Route::get('/github/pull-request/logs', [GithubPrWebhooksController::class, 'githubPrs'])->name('githubPrs.index');
    Route::get('/pull_requests', [DeployController::class, 'pullrequest']);
    Route::post('/merge-pull-request', [DeployController::class, 'mergePullRequest'])->name('merge-pull-request');

    Route::prefix('feeds')->group(function () {
        Route::get('/', [FeedController::class, 'index'])->name('feeds.index');
        Route::get('/show', [FeedController::class, 'show'])->name('feeds.show');
    });

    Route::get('/pusher/beams-auth', function (Request $request, NotificationService $notification) {
        $beamsToken = $notification->auth(auth()->user()->email);

        return response()->json($beamsToken);
    })->name('beams.auth');

    Route::prefix('watchLists')->group(function () {
        Route::get('/', [TaskWatchListController::class, 'index'])->name('task.watchlists.index');
        Route::post('/store', [TaskWatchListController::class, 'store'])->name('task.watchlists.store');
        Route::post('/delete', [TaskWatchListController::class, 'destroy'])->name('task.watchlists.destroy');
    });

    Route::prefix('dailyFeeds')->group(function () {

        Route::get('/', [DailyFeedController::class, 'index'])->name('dailyFeeds.index');
        Route::get('/show', [DailyFeedController::class, 'show'])->name('dailyFeeds.show');
    });
});

/*
|--------------------------------------------------------------------------
| Routes without middleware
|--------------------------------------------------------------------------
*/
Route::prefix('cronJobs')->group(function () {
    Route::get('/retrieveComments', [CronJobController::class, 'retrieveComments']);
    Route::get('/getUnreportedTasks', [CronJobController::class, 'unreportedTasks']);
    Route::get('/archiveCompletedTasks', [CronJobController::class, 'archiveCompletedTasks']);
    Route::get('/createTask', [CronJobController::class, 'createTask']);
    Route::get('/archiveCompletedTasks', [CronJobController::class, 'archiveCompletedTasks']);
    Route::get('/notifyTasks', [CronJobController::class, 'notifyTasks']); // notifies pending tasks to assignees and reviewers
    Route::get('/uncompletedTask', [CronJobController::class, 'uncompletedTaskEmail'])->name('uncompletedTask');
    Route::get('/mergeBranches', [CronJobController::class, 'mergeBranches']);
    Route::get('/mergePrs/{projectId}/{prNumber}', [CronJobController::class, 'mergePrs']);

    Route::get('/config', [CronJobController::class, 'configure']);
    Route::get('/notify/deploy/logs', [CronJobController::class, 'deployStatus']);
    Route::get('/taskToReview', [CronJobController::class, 'taskToReview']);
});
Route::prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'getLatestNotifications']);
});

Route::get('/tasks/{id}/status/{status}', [TasksCommentController::class, 'notifyCicdAlert'])->name('notifyCicdAlert');
