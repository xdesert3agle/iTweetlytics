<?php

namespace App\Http\Controllers;

use App\Report;
use Illuminate\Http\Request;

class LandingPageController extends Controller {
    public function index() {
        return view('landing')->with([
            'reports' => Report::all()->toArray()
        ]);
    }
}
