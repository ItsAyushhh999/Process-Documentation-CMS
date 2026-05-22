<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Http\Services\GitWebhooksService;
use App\Models\GithubWebhook;
use App\Models\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Request;

class GithubPrWebhooksController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(Request $request, GitWebhooksService $gitWebhooks): JsonResponse
    {
        $payLoad = json_decode($request->getContent(), true);
        $gitWebhook = null;

        $ignoreAction = [
            'review_requested',
            'review_request_removed',
        ];

        try {
            $action = data_get($payLoad, 'action');
            $comment = data_get($payLoad, 'comment');
            $review = data_get($payLoad, 'review');
            $pullRequest = $payLoad['pull_request'] ?? null;

            if (($action === 'created' && $comment)) {
                $gitWebhook = $gitWebhooks->webhooks($payLoad, type: 1);
            } elseif (($pullRequest && !in_array($action, $ignoreAction)) || $review) {
                $gitWebhook = $gitWebhooks->webhooks($payLoad);
            }

            if ($gitWebhook) {

                $shouldPin = in_array(
                    Str::lower(data_get($payLoad, 'pull_request.base.ref', '')),
                    ['production', 'main', 'master'],
                    true
                );

                $taskComment = $gitWebhooks->commentTask($gitWebhook, null, shouldPin: $shouldPin);

                if ($taskComment) {
                    $gitWebhooks->notificationTask($taskComment, null);
                }
            }
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        } catch (GuzzleException $e) {
            Log::error($e->getMessage());
        }

        return response()->json('ok');
    }

    /**
     * @param Request $request
     * @return \Inertia\Response
     */
    public function githubPrs(Request $request)
    {
        $users = User::select('id', 'name', 'profile_picture')->latest()->get();
        $githubWebhooks = GithubWebhook::with('task:id,title', 'user:id,name')
                                        ->when($request->task_id, fn($q) => $q->where('task_id', $request->task_id))
                                        ->orderBy('id', 'desc')
                                        ->get();

        if ($request->task_id) {
            return response()->json([
                'githubWebhooks' => $githubWebhooks,
            ]);
        }
        
        return Inertia::render('GithubWebHooks/index', [
            'githubWebhooks' => $githubWebhooks,
            'users' => $users,
        ]);
    }
}
