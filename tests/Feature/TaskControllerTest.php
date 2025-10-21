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

    /* show*/
    public function test_show_my_task_is_ok_but_ohter_is_forbidden():void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        $mine = Task::factory()->for($me)->create();
        $others = Task::factory()->for($other)->create();

        // コントローラ実装は403リダイレクト/ステータス扱いのどちらか
        $this->actingAs($me)->get("/tasks/{$others->id}")
                ->assertStatus(404);
    }

    //update

    public function test_update_patch_only_set_fieles():void
    {
        $me = User::factory()->create();
        $task = Task::factory()->for($me)->create([
            'title' => 'old',
            'is_done'=>false,
        ]);

        //is_doneだけ変更、titleは据え置き
        $res = $this->actingAs($me)->patch(route('tasks.update',$task),[
            'title' => $task->title,
            'is_done'=>true,
        ]);

        $res->assertRedirect(route('tasks.index'))
            ->assertSessionHas('status','taskを更新しました');

        $this->assertDatabaseHas('tasks',[
            'id' =>$task->id,
            'title'=>'old',
            'is_done'=>true,
        ]);
    }

    public function test_update_validation_error_redirects_back_with_errors(): void
    {
        $me = User::factory()->create();
        $task = Task::factory()->for($me)->create(['title'=>'keep']);

        $this->actingAs($me)
            ->from("/tasks/{$task->id}/edit")
            ->patch("/tasks/{$task->id}",['title'=>''])
            ->assertRedirect("/tasks/{$task->id}/edit")
            ->assertSessionHasErrors(['title']);

        $this->assertSame('keep', $task->fresh()->title);
    }

    //destory
    public function test_destroy_my_task():void
    {
        $me = User::factory()->create();
        $task = Task::factory()->for($me)->create();

        $this->actingAs($me)->delete("tasks/{$task->id}")
                ->assertRedirect(route('tasks.index'))
                ->assertSessionHas('status','タスクが削除されました');
        
        $this->assertDatabaseMissing('tasks',['id'=>$task->id]);
    }

    public function test_destroy_others_task_is_forbidden(): void
    {
        $me     = User::factory()->create();
        $other  = User::factory()->create();
        $others = Task::factory()->for($other)->create();

        $this->actingAs($me)
            ->delete(route('tasks.destroy', $others))
            ->assertRedirect(route('tasks.index'))
            ->assertSessionHas('error', 'tasksを削除する権限がありません');

        $this->assertDatabaseHas('tasks', ['id' => $others->id, 'user_id' => $other->id]);
        $this->assertDatabaseCount('tasks', 1);
    }
}
