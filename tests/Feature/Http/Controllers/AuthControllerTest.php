<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * 測試 csrf cookie 成功
     *
     * @return void
     */
    public function test_get_csrf_cookie_success()
    {
        $response = $this->get('/api/auth/csrf-cookie');

        $response->assertStatus(302);
    }
}
