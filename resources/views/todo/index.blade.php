<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo Page</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.16/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Todo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">

                <!-- CREATE -->
                <div class="overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg mb-6">
                    <div class="p-6 text-xl text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <x-create-button href="{{ route('todo.create') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow-md hover:bg-blue-700 transition duration-200">
                                    Add Todo
                                </x-create-button>
                            </div>

                            @if (session('success'))
                                <p x-data="{ show: true }" x-show="show" x-transition
                                   x-init="setTimeout(() => show = false, 5000)"
                                   class="text-sm text-green-600 dark:text-green-400">
                                    {{ session('success') }}
                                </p>
                            @endif

                            @if (session('danger'))
                                <p x-data="{ show: true }" x-show="show" x-transition
                                   x-init="setTimeout(() => show = false, 5000)"
                                   class="text-sm text-red-600 dark:text-red-400">
                                    {{ session('danger') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Todo Table -->
                <div class="relative overflow-x-auto shadow-lg sm:rounded-lg bg-white dark:bg-gray-800">
                    <table class="min-w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($todos as $data)
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('todo.edit', $data) }}" class="text--600 dark:text-blue-400 hover:underline text-sm">{{ $data->title }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($data->is_done == false)
                                        <span class="inline-flex items-center bg-red-100 text-red-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-lg dark:bg-red-900 dark:text-red-300">
                                            On Going
                                        </span>
                                    @elseif ($data->is_done == true)
                                        <span class="inline-flex items-center bg-green-100 text-green-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded-lg dark:bg-green-900 dark:text-green-300">
                                            Done
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-4">
                                        @if ($data->is_done == false)
                                            <form action="{{ route('todo.complete', $data) }}" method="Post">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 dark:text-green-400 text-sm px-4 py-2 rounded-lg bg-green-100 hover:bg-green-200 transition duration-200">
                                                    Complete
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('todo.uncomplete', $data) }}" method="Post">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 dark:text-blue-400 text-sm px-4 py-2 rounded-lg bg-blue-100 hover:bg-blue-200 transition duration-200">
                                                    Uncomplete
                                                </button>
                                            </form>
                                        @endif
                                        <!-- Delete Form -->
                                        <form action="{{ route('todo.destroy', $data) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 text-sm px-4 py-2 rounded-lg bg-red-100 hover:bg-red-200 transition duration-200">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No data available
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- End Todo Table -->

                <!-- Delete All Completed Tasks -->
                @if ($todosCompleted > 1)
                    <div class="p-6 text-xl text-gray-900 dark:text-gray-100">
                        <form action="{{ route('todo.deleteallcompleted') }}" method="Post">
                            @csrf
                            @method('DELETE')
                            <x-primary-button class="px-6 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition duration-200">
                                Delete All Completed Task
                            </x-primary-button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

</body>
</html>
