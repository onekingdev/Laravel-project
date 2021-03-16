<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App;
use App\File;

class MainController extends Controller
{
    public function index()
    {
        return view('main');
    }

    public function modelview($id)
    {
        $model = new File();
        $file = $model::where('id', $id)->first();
        return view('layouts.filedata', compact('file'));
    }
}