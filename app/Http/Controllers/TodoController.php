<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TodoController extends Controller
{
    public function index()
    {
        // $todos = Todo::with('category') // <-- eager load
        // ->where('user_id', auth()->user()->id)
        // ->orderBy('is_done', 'asc')
        // ->orderBy('created_at', 'desc')
        // ->get();

        // // Hitung jumlah todo yang telah selesai milik pengguna
        // $todosCompleted = Todo::where('user_id', auth()->user()->id)
        //         ->where('is_done', true) // Hanya yang selesai
        //         ->count();

        // // Kirim data ke view
        // return view('todo.index', compact('todos', 'todosCompleted'));
         $todos = Todo::with('category')
        ->where('user_id', Auth::id())
        ->orderBy('is_done', 'asc')
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        $todoCompleted = Todo::where('user_id', Auth::id())
        ->where('is_done', true)
        ->count();

        return view('todo.index', compact('todos', 'todoCompleted'));
    }    
    public function create()
    {
        $categories = Category::all();
        // dd($categories); 
        return view('todo.create', compact('categories')); 
    }
    public function edit(Todo $todo)
    {
        $categories = Category::where('user_id', Auth::id())->get();
        if (auth()->user()->id == $todo->user_id) {
            // dd($todo);
            $categories = Category::all();
            return view('todo.edit', compact('todo', 'categories'));
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
            'category_id' => $request->category_id,
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
            'category_id' => 'nullable|exists:categories,id'
        ]);

        // Practical
        // $todo->title = $request->title;
        // $todo->save();

        // Eloquent Way - Readable
        $todo->update([
            'title' => ucfirst($request->title),
            'category_id' => $request->category_id
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
