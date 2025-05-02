<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    public function index()
    {
        // Ambil semua todo milik pengguna yang sedang login, urutkan berdasarkan status dan tanggal
        $todos = Todo::where('user_id', auth()->user()->id)
        ->orderBy('is_done', 'asc') // Urutkan berdasarkan status (belum selesai di atas)
        ->orderBy('created_at', 'desc') // Urutkan berdasarkan tanggal terbaru
        ->get();

        // Hitung jumlah todo yang telah selesai milik pengguna
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
                ->where('is_done', true) // Hanya yang selesai
                ->count();

        // Kirim data ke view
        return view('todo.index', compact('todos', 'todosCompleted'));
    }    
    public function create()
    {
        return view('todo.create');
    }
    public function edit(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            // dd($todo);
            return view('todo.edit', compact('todo'));
        } else {
            // abort(403);
            // abort(403, 'Not authorized');
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to edit this todo!');
        }
    }
    
    public function store(Request $request)
    {
         $request->validate([
            'title' => 'required|string|max:255',
         ]);

         $todo = Todo::create([
            'title' => ucfirst($request->title),
            'user_id' => Auth::id(),
        ]);
        return redirect()->route('todo.index')->with('success', 'Todo created successfully.');
    }

    public function complete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_done' => true,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo completed successfully!');
        } else {
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to complete this todo!');
        }
    }

    public function uncomplete(Todo $todo)
    {
        if (auth()->user()->id == $todo->user_id) {
            $todo->update([
                'is_done' => false,
            ]);
            return redirect()->route('todo.index')->with('success', 'Todo uncompleted successfully!');
        } else {
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to uncomplete this todo!');
        }
    }

    public function update(Request $request, Todo $todo)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Practical
        // $todo->title = $request->title;
        // $todo->save();

        // Eloquent Way - Readable
        $todo->update([
            'title' => ucfirst($request->title),
        ]);

        return redirect()->route('todo.index')->with('success', 'Todo updated successfully!');
    }

    public function destroy(Todo $todo)
    {
        // Pastikan pengguna yang sedang login adalah pemilik todo
        if (auth()->user()->id === $todo->user_id) {
            $todo->delete();
            return redirect()->route('todo.index')
                            ->with('success', 'Todo deleted successfully!');
        }

        // Jika bukan pemilik, kirimkan pesan error
        return redirect()->route('todo.index')
                        ->with('danger', 'You are not authorized to delete this todo!');
    }

    public function destroyCompleted()
    {
        // Ambil semua todo yang sudah selesai milik pengguna yang sedang login
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
                            ->where('is_done', true)
                            ->get();

        // Hapus setiap todo yang sudah selesai
        $todosCompleted->each(function ($todo) {
            $todo->delete();
        });

        return redirect()->route('todo.index')
                        ->with('success', 'All completed todos deleted successfully!');
    }
}
