<?php

namespace App\Http\Controllers;

use App\Report;
use App\TwitterProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller {
    public function getReportStat($profileId, $stat, $timeInterval) {
        $now = Carbon::now();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $twoWeeksAgo = Carbon::now()->subWeeks(2)->startOfDay();
        $monthAgo = Carbon::now()->subMonth()->startOfDay();
        $yearAgo = Carbon::now()->subYear()->startOfDay();

        $isUserOwnerOfProfile = TwitterProfile::find($profileId)->belongsToUser(Auth::id());

        $reports = [];

        if ($isUserOwnerOfProfile) {
            switch ($stat) {
                case 'followers':
                    $attr = 'profile_total_followers';
                    break;

                case 'unfollows':
                    $attr = 'unfollows';
                    break;
            }

            switch ($timeInterval) {
                case 'weekly':
                    $reports = Report::whereBetween('created_at', [$weekAgo, $now])
                        ->where('twitter_profile_id', $profileId)
                        ->get()
                        ->groupBy(function ($val) {
                            return Carbon::parse($val->created_at)->format('d-m');
                        });
                    break;

                case 'biweekly':
                    $reports = Report::whereBetween('created_at', [$twoWeeksAgo, $now])
                        ->where('twitter_profile_id', $profileId)
                        ->get()
                        ->groupBy(function ($val) {
                            return Carbon::parse($val->created_at)->format('d-m');
                        });
                    break;

                case 'monthly':
                    $reports = Report::whereBetween('created_at', [$monthAgo, $now])
                        ->where('twitter_profile_id', $profileId)
                        ->get()
                        ->groupBy(function ($val) {
                            return Carbon::parse($val->created_at)->format('d');
                        });

                    break;

                case 'yearly':
                    $reports = Report::whereBetween('created_at', [$yearAgo, $now])
                        ->where('twitter_profile_id', $profileId)
                        ->get()
                        ->groupBy(function ($val) {
                            return Carbon::parse($val->created_at)->format('d-m');
                        });

                    break;
            }

            $formatted = [];

            foreach ($reports as $i => $report) {
                $formatted[$i] = $report[0]->$attr;
            }
        }

        return $formatted;
    }
}
