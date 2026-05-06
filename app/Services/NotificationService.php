<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Pusher\PushNotifications\PushNotifications;

class NotificationService
{
    private PushNotifications $pusher;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->pusher = new PushNotifications([
            'instanceId' => config('services.pusher.INSTANCE_ID'),
            'secretKey' => config('services.pusher.PRIMARY_KEY'),
        ]);

    }

    /**
     * @param $userId
     * @param $title
     * @param $message
     *
     * This function will send notification to users
     */
    public function notifyUser($userId, $title, $message, $url = null): void
    {
        $userIds = $userId;

        if (!is_array($userId)) {
            $userIds = [$userId];
        }

        try {

            $this->pusher->publishToUsers($userIds, [
                'web' => ['notification' => ['title' => $title, 'body' => $message, 'deep_link' => $url]],
            ]);

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    /**
     * @param $userId
     * @return array This function generate a beam token by taking userId
     * This function generate a beam token by taking userId
     */
    public function auth($userId): array
    {
        return $this->pusher->generateToken($userId);
    }
}
