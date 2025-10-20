<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Create Task</h2>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6">
        @if(session('status'))
            <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{session('status')}}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{route('tasks.store')}}" method="POST">
            @csrf

            {{-- Title --}}
            <div class="mb-4">
                <label class="block mb-1 font-semibold">
                    タイトル<span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" value="{{old('title',$task->title)}}"
                    class="w-full border rounded px-3 py-2" maxlength="100" required>
            </div>
        </form>
    </div>
</x-app-layout>