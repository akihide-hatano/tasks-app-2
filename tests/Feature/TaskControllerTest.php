<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskControllerTest extends TestCase
{

    use RefreshDatabase;
    /* ---------- Auth redirect ---------- */

    public function test_guest_is_redirected_to_login():void
    {
        $this->get('tasks')->assertRedirect('/login');
        $this->post('/tasks',[])->assertRedirect('/login');
    }

    /* ---------- index ---------- */
    public function test_index_shows_only_my_tasks():void
    {
        $me    = User::factory()->create();
        $other = User::factory()->create();

        Task::factory()->for($me)->count(2)->create(['title' => 'mine']);
        Task::factory()->for($other)->count(1)->create(['title' => 'others']);

        $res = $this->actingAs($me)->get('/tasks');

        $res->assertOk()
            ->assertViewIs('tasks.index')
            ->assertSee('mine')
            ->assertDontSee('others'); // 他人のタスクは表示されない
    }

    public function test_index_pagenates():void
    {
        $me = User::factory()->create();
        Task::factory()->for($me)->count(15)->create();

        $this->actingAs($me)->get('/tasks?page=1')
            ->assertOk()
            ->assertViewIs('tasks.index');
    }

    /* ---------- store ---------- */
    public function test_store_creates_task_and_normalizes_title():void
    {
        $me = User::factory()->create();
        $res = $this->actingAs($me)->post('/tasks',[
            'title' => 'テ ス ト'
        ]);

        $res->assertRedirect(route('tasks.index'))
        ->assertSessionHas('status', 'Task created.');

        // モデル側のタイトル正規化（半角スペ1個へ圧縮）を前提とした期待値
        $this->assertDatabaseHas('tasks', [
            'user_id' => $me->id,
            'title'   => 'テ ス ト',
            'is_done' => false,
        ]);
    }

    public function test_store_validation_title_required_and_max(): void
    {
        $me = User::factory()->create();

        //titleなし
        $this->actingAs($me)->from('/tasks/create')
                ->post('/tasks',[])
                ->assertRedirect('/tasks/create')
                ->assertSessionHasErrors(['title']);

        //101文字以上はNG
        $toolong = str_repeat('a',101);
        $this->actingAs($me)->from('/tasks/create')
            ->post('/tasks',['title'=>$toolong])
            ->assertRedirect('tasks/create')
            ->assertSessionHasErrors(['title']);

        //100文字はOK
        $ok = str_repeat('a',100);
        $res = $this->actingAs($me)->from('/tasks/create')
                ->post('tasks',['title'=>$ok]);

        $res ->assertRedirect(route('tasks.index'))
                ->assertSessionHas('status','Task created.');

        $this->assertDatabaseHas('tasks',[
                    'user_id' => $me->id,
                    'title'   => $ok,
                    'is_done' => false,
                ]);
    }

    public function test_store_without_is_done_defaults_false():void
    {
        $me = User::factory()->create();

        $this->actingAs($me)->post('/tasks',
                ['title'=>'x'])
                ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks',[
            'user_id' => $me->id,
            'title'   => 'x',
            'is_done' => false,
        ]);

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
