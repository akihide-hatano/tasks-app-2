<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function index()
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $tasks = Task::whereBelongsTo($user)->latest()->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $task = new Task(); // ← 変数名を揃える
        return view('tasks.create', compact('task'));
    }

    public function store(StoreTaskRequest $request) // ← FormRequest を受ける
    {
        /** @var User $user */
        $user = Auth::user();

        if(!$user){
            return redirect()->route('login');
        }

        $user->tasks()->create($request->validated());
        return redirect()
            ->route('tasks.index')
            ->with('status', 'Task created.');
    }

    public function show(string $id) {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }


        $task = $user->tasks()->findOrFail($id);
        return view('tasks.show',compact('task'));
    }
    public function edit(Task $task) {
        if($task->user_id !== Auth::id()){
            //権限がないので一覧に戻るように設定
            return redirect()
                    ->route('tasks.index')
                    ->with('error', 'You are not allowed to edit that task.');
        }

        return view('tasks.edit',compact('task'));

    }
    public function update(UpdateTaskRequest $request, Task $task) {
        if($task->user_id !== Auth::id()){
            return back()
                    ->withInput()
                    ->with('error','taskを保存する権限がありません');
        }

        $task->update($request->validated());

        return redirect()
                ->route('tasks.index')
                ->with('status','taskを更新しました');
    }

    public function destroy(Task $task) {
        if($task->user_id !== Auth::id()){
            return redirect()
                    ->route('tasks.index')
                    ->with('error','tasksを削除する権限がありません');
        }

        $task->delete();
        return redirect()
                ->route('tasks.index')
                ->with('status','タスクが削除されました');
    }

}
