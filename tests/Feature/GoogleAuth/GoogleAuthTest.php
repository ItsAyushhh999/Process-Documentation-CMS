<?php

namespace Tests\Feature\GoogleAuth;

use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_google_authentication()
    {
        $user = (object) [
            'id' => 1224,
            'name' => 'John Doe',
            'email' => 'keshav.a@shikhartech.com',
            'token' => 'dkmd390p2jelk20poejldm',
            'refreshToken' => '3u9027jk',
        ];

        Socialite::shouldReceive('driver->user')->andReturn($user);

        $response = $this->get('/auth/google/call-back');

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    public function test_google_redirect()
    {
        $socialite = Socialite::shouldReceive('driver->redirect');

        $response = $this->get('/auth/redirect');
        $response->assertStatus(200);
    }
}
