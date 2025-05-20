<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        // $search = request('search');
        // if ($search) {
        //     $users = User::where(function($query) use ($search) {
        //                 $query->where('name', 'like', '%'.$search.'%')
        //                     ->orWhere('email', 'like', '%'.$search.'%');
        //             })
        //             ->where('id', '!=', '1')
        //             ->orderBy('name')
        //             ->paginate(20)
        //             ->withQueryString();
        // } else {
        //     $users = User::where('id', '!=', '1')
        //             ->orderBy('name')
        //             ->paginate(10);
        // }
        // return view('user.index', compact('users'));
        $search = request('search');
        if ($search) {
            $users = User::with('todos')->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        } else {
            $users = User::with('todos')->where('id', '!=', 1)
                ->orderBy('name')
                ->paginate(10);
    }
    return view('user.index', compact('users'));
    }

    public function makeadmin(User $user)
    {
        $user->timestamps = false;
        $user->is_admin = true;
        $user->save();
        return back()->with('success', 'Make admin successfully!');
    }

    public function removeadmin(User $user)
    {
        if ($user->id != 1) {
            $user->timestamps = false;
            $user->is_admin = false;
            $user->save();
            return back()->with('success', 'Remove admin successfully!');
        } else {
            return redirect()->route('user.index');
        }
    }

    public function destroy(User $user)
    {
        // Cek jika user yang akan dihapus bukan user dengan ID 1
        if ($user->id != 1) {
            // Hapus user
            $user->delete();

            // Redirect kembali dengan pesan sukses
            return back()->with('success', 'User deleted successfully!');
        }

        // Jika user dengan ID 1, tidak bisa dihapus
        return redirect()->route('user.index')->with('danger', 'Cannot delete this user!');
    }
}
