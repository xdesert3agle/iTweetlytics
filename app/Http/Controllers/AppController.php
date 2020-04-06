<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller {
    public function index() {
        $user = User::with('twitter_profiles')->find(Auth::id());

        return view('app')->with([
            'user' => $user
        ]);
    }
}
