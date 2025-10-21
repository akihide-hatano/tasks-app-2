<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Create Task</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST">
            @csrf

            {{-- Title --}}
            <div class="mb-4">
                <label class="block mb-1 font-medium">
                    Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text" name="title" value="{{ old('title', $task->title) }}"
                    class="w-full border rounded px-3 py-2" maxlength="100" required >
                @error('title')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- is_done --}}
            <div class="mb-6">
                {{-- 未チェックでも値が送られるよう hidden を入れておく --}}
                <input type="hidden" name="is_done" value="0">
                <label class="inline-flex items-center gap-2">
                    <input
                        type="checkbox"
                        name="is_done"
                        value="1"
                        @checked(old('is_done', (int)$task->is_done) == 1)
                    >
                    <span>Done</span>
                </label>
                @error('is_done')
                    <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 border rounded">Cancel</a>
            </div>
        </form>
    </div>
</x-app-layout>
