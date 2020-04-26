<?php

namespace App\Http\Controllers;


use App\Follower;
use App\Follow;
use App\Friend;
use App\Report;
use App\TwitterProfile;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Thujohn\Twitter\Facades\Twitter;

class TestController extends Controller {

    public function test() {
        /*$now = Carbon::now();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $twoWeeksAgo = Carbon::now()->subWeeks(2)->startOfDay();
        $monthAgo = Carbon::now()->subMonth()->startOfDay();
        $yearAgo = Carbon::now()->subYear()->startOfDay();

        $fromTime = "";
        $stats = json_decode('{}');

        switch ($r->timeInterval) {
            case 'weekly':
                $fromTime = $weekAgo;
                break;

            case 'biweekly':
                $fromTime = $twoWeeksAgo;
                break;

            case 'monthly':
                $fromTime = $monthAgo;
                break;

            case 'yearly':
                $fromTime = $yearAgo;
                break;
        }

        $user = User::find(Auth::id())
            ->with('twitter_profiles')
            ->with(['current_twitter_profile' => function ($query) use ($selectedProfileIndex, $fromTime, $now) {
                $query->with('followers')
                    ->with('reports')
                    ->with('follows')
                    ->with('unfollows')
                    ->whereBetween('created_at', [$fromTime, $now])
                    ->orderBy('created_at')
                    ->skip($selectedProfileIndex)->take(1);
            }])
            ->first();

        dd($user);*/
    }
}
