<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
</head>
<body>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('User') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    {{-- Form Pencarian --}}
                    <div class="px-6 pt-6 mb-8 w-full sm:w-2/3 md:w-1/2 lg:w-1/3">
                        <form class="flex items-center gap-3" method="GET" action="{{ route('user.index') }}">
                            <x-text-input 
                                id="search" name="search" type="text" 
                                class="w-full md:w-5/6 px-4 py-2 rounded-md border border-gray-300 dark:border-gray-600 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400" 
                                placeholder="Search by name or email ..." 
                                value="{{ request('search') }}" autofocus 
                                aria-label="Search" 
                            />
                            <x-primary-button type="submit" class="px-4 py-2 rounded-md ml-2">
                                {{ __('Search') }}
                            </x-primary-button>
                        </form>
                    </div>

                    {{-- Hasil Pencarian --}}
                    @if (request('search'))
                        <div class="px-6 mt-4 text-xl text-gray-900 dark:text-gray-100 mb-4">
                            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                                Search results for: <strong>{{ request('search') }}</strong>
                            </h2>
                        </div>
                    @endif

                    {{-- Alert --}}
                    <div class="px-6 text-xl text-gray-900 dark:text-gray-100">
                        @if (session('success'))
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 5000)"
                               class="pb-3 text-sm text-green-600 dark:text-green-400">
                                {{ session('success') }}
                            </p>
                        @endif

                        @if (session('danger'))
                            <p x-data="{ show: true }" x-show="show" x-transition
                               x-init="setTimeout(() => show = false, 5000)"
                               class="pb-3 text-sm text-red-600 dark:text-red-400">
                                {{ session('danger') }}
                            </p>
                        @endif
                    </div>

                    {{-- Tabel User --}}
                    <div class="relative overflow-x-auto mb-6">
                        @if (request('search') && $users->isEmpty())
                            <div class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                No results found for "{{ request('search') }}"
                            </div>
                        @else
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Id</th>
                                        <th scope="col" class="px-6 py-3">Nama</th>
                                        <th scope="col" class="hidden px-6 py-3 md:block">Email</th>
                                        <th scope="col" class="px-6 py-3">Todo</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $data)
                                        <tr class="odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-700">
                                            <td class="px-6 py-4 font-medium whitespace-nowrap dark:text-white">
                                                {{ $data->id }}
                                            </td>
                                            <td class="px-6 py-4">{{ $data->name }}</td>
                                            <td class="hidden px-6 py-4 md:block">{{ $data->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p>
                                                    {{ $data->todos->count() }}
                                                    <span>
                                                        <span class="text-green-600 dark:text-green-400">
                                                            ({{ $data->todos->where('is_done', true)->count() }})
                                                        </span>/ 
                                                        <span class="text-blue-600 dark:text-blue-400">
                                                            {{ $data->todos->where('is_done', false)->count() }}
                                                        </span>
                                                    </span>
                                                </p>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    @if ($data->is_admin)
                                                        <form action="{{ route('user.removeadmin', $data) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-semibold rounded hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">
                                                                Remove Admin
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('user.makeadmin', $data) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-800 text-xs font-semibold rounded hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-900 dark:text-green-300 dark:hover:bg-green-800">
                                                                Make Admin
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('user.destroy', $data) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-800 text-xs font-semibold rounded hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:bg-red-900 dark:text-red-300 dark:hover:bg-red-800">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- Pagination --}}
                    <div class="px-6 py-5">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>
        </div>
    </x-app-layout>
</body>
</html>
