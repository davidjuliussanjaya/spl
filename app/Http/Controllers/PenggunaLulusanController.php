<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenggunaLulusanController extends Controller
{
    public function index()
    {
        return view('admin.penggunalulusan.index');
    }
}
