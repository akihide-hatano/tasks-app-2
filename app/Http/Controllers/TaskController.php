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
        /** @var User $user */
        $user = Auth::user() ?? abort(401);

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
        $user = Auth::user() ?? abort(401);

        $user->tasks()->create($request->validated());
        return redirect()
            ->route('tasks.index')
            ->with('status', 'Task created.');
    }

    public function show(Task $task) {}
    public function edit(Task $task) {}
    public function update(UpdateTaskRequest $request, Task $task) {}
    public function destroy(Task $task) {}
}
