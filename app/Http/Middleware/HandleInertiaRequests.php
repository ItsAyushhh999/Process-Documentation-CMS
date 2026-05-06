<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // 'auth' => [
            //     'user' => $request->user(),
            //     'firstLogin' => $request->session()->get('firstLogin'),
            // ],

            'auth' => [
                'user' => function () use ($request) {
                    if ($user = $request->user()) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'phone' => $user->phone,
                            'department_id' => $user->department_id,
                            'email' => $user->email,
                            'github_username' => $user->github_username,
                            'slack_username' => $user->slack_username,
                            'is_super_admin' => $user->is_super_admin,
                            'profile_picture' => $user->profile_picture,
                            'title_id' => $user->title_id,
                        ];
                    }

                    return null;
                },
                'firstLogin' => $request->session()->get('firstLogin'),
            ],
            'config' => [
                'app' => [
                    'authorized_id' => config('app.authorized_id'),
                ],
            ],
            'ziggy' => function () use ($request) {
                return array_merge((new Ziggy)->toArray(), [
                    'location' => $request->url(),
                ]);
            },
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            },
            'flashMessage' => [], // Initialize as an empty array
        ]);
    }
}
