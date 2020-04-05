<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller {
    public function index() {
        $user = User::with('twitter_profiles')->find(Auth::id());

        return view('dashboard')->with([
            'user' => $user
        ]);
    }
}
