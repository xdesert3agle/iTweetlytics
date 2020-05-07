<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller {
    public function index() {
        return view('sync')->with([
            'user' => Auth::user()
        ]);
    }
}
