<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin') // hanya user non-admin
            ->latest()
            ->paginate(10);

        $total = User::where('role', '!=', 'admin')->count(); // hitung total non-admin

        return view('admin.users.index', compact('users', 'total'));

        
    }
}
