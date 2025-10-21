<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl">Task Detail</h2>
  </x-slot>

  <div class="max-w-3xl mx-auto p-6">
    <div class="border rounded p-4">
      <div class="mb-2">
        <span class="text-gray-500 text-sm">ID:</span> {{ $task->id }}
      </div>
      <div class="mb-2">
        <span class="text-gray-500 text-sm">Title:</span> {{ $task->title }}
      </div>
      <div>
        <span class="text-gray-500 text-sm">Status:</span>
        <span class="{{ $task->is_done ? 'text-green-600' : 'text-gray-600' }}">
          {{ $task->is_done ? 'Done' : 'Open' }}
        </span>
      </div>
    </div>

    <div class="mt-4 flex gap-2">
      <a href="{{ route('tasks.edit', $task) }}" class="px-4 py-2 border rounded bg-green-600 text-white">Edit</a>
      <a href="{{ route('tasks.index') }}" class="px-4 py-2 border rounded">Back</a>
    </div>
  </div>
</x-app-layout>
