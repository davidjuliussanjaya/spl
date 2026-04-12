<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LulusanController extends Controller
{
    public function index()
    {
        return view('admin.lulusan.index');
    }
    public function add()
    {
        return view('admin.lulusan.add');
    }
}
