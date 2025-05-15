<x-app-layout>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        @vite('resources/css/app.css')
    </head>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Todo Category') }}
        </h2>
    </x-slot>

    <div class="py-12 flex justify-center items-center">
        <div class="w-full max-w-7xl bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-xl text-gray-900 dark:text-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                    <a href="{{ route('category.create')}}" 
                    class="px-3 py-2 text-sm bg-white text-gray-900 border border-gray-300 rounded-md shadow-sm hover:bg-gray-100">
                    {{ __('CREATE') }}
                    </a>
                    </div>
                </div>
                @if (session('success'))
                    <p x-data="{ show: true }" x-show="show" x-transition
                        x-init="setTimeout(() => show = false, 5000)"
                        class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}
                    </p>
                @endif
                @if (session('danger'))
                    <p x-data="{ show : true }" x-show="show" x-transition
                        x-init="setTimeout(() => show = false, 5000)"
                        class="text-sm text-red-600 dark:text-red-400">{{ session('danger') }}
                    </p>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left">Title</th>
                                <th scope="col" class="px-6 py-3">Todo</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr class="border-b border-gray-600 dark:border-gray-500">
                                    <td class="px-6 py-4 font-medium text-white dark:text-white">
                                        <a href="{{ route('category.edit', $category) }}" class="hover:underline text-xs">
                                            {{ $category->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4">{{ $category->todos->count() }}</td>
                                    <td class="px-6 py-4">
                                        <form action="{{ route('category.destroy', $category) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>