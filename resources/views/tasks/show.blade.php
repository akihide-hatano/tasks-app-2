<x-app-layout>
    <x-slot name="header">
    <h2 class="font-semibold text-xl">Tasks</h2>
    </x-slot>

<div class="max-w-3xl mx-auto p-6">
    @if(session('status'))
        <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>
    @endif

<div class="mb-4">
    <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">New Task</a>
</div>

@forelse($tasks as $task)
    <div class="border rounded p-3 mb-2 flex items-center justify-between">
    <div>
        <a class="font-medium underline" href="{{ route('tasks.show', $task) }}">
        {{ $task->title }}
        </a>
        <span class="ml-2 text-sm {{ $task->is_done ? 'text-green-600' : 'text-gray-500' }}">
        {{ $task->is_done ? 'Done' : 'Open' }}
        </span>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('tasks.edit', $task) }}" class="px-3 py-1 border rounded">Edit</a>
        <form method="POST" action="{{ route('tasks.destroy', $task) }}">
        @csrf @method('DELETE')
        <button class="px-3 py-1 bg-red-600 text-white rounded" onclick="return confirm('Delete?')">Delete</button>
        </form>
    </div>
    </div>
    @empty
        <p class="text-gray-500">No tasks yet.</p>
    @endforelse

    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
</x-app-layout>
