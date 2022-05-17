<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home() 
    {
        /*
        Auth::id();
        Auth::check();
        */

        // dd(Auth::user());

        return view('home.index');
    }

    public function contact() 
    {
        return view('home.contact');
    }
}
