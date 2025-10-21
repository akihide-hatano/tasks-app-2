<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{

    use RefreshDatabase;
    /* ---------- Auth redirect ---------- */

    public function test_guest_is_redirected_to_login():void
    {
        $this->get('tasks')->assertRedirect('/login');
        $this->post('/tasks',[])->assertRedirect('/login');
    }
    
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
