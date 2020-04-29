<?php

namespace App\Http\Controllers;

use App\Report;
use App\TwitterProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller {
    public function getStat($profileId, $stat, $timeInterval) {
        $now = Carbon::now();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $twoWeeksAgo = Carbon::now()->subWeeks(2)->startOfDay();
        $monthAgo = Carbon::now()->subMonth()->startOfDay();
        $yearAgo = Carbon::now()->subYear()->startOfDay();

        $isUserOwnerOfProfile = TwitterProfile::find($profileId)->belongsToUser(Auth::id());

        $reports = [];
        $attr = "";
        $is_accum = false;

        if ($isUserOwnerOfProfile) {
            switch ($stat) {
                case 'followers':
                    $attr = 'total_followers';
                    $is_accum = false;
                    break;

                case 'follows':
                    $attr = 'follows';
                    $is_accum = true;
                    break;

                case 'unfollows':
                    $attr = 'unfollows';
                    $is_accum = true;
                    break;

                case 'friends':
                    $attr = 'total_friends';
                    $is_accum = false;
                    break;

                case 'befriends':
                    $attr = 'befriends';
                    $is_accum = true;
                    break;

                case 'unfriends':
                    $attr = 'unfriends';
                    $is_accum = true;
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

            $graph_data = [];
            $latest_report = "";
            $accum_variation = 0;
            $accum_value = 0;

            $prev_report = (object)array_values($reports->toArray())[0][0]; // Al principio el report anterior es el primero

            foreach ($reports as $i => $report) {
                $latest_report_index = count($report) - 1;
                $latest_report = (object)$report[$latest_report_index]->toArray();

                $accum_value += $latest_report->$attr;
                $current_variation = $latest_report->$attr - $prev_report->$attr;
                $accum_variation += $current_variation;

                $graph_data[$i] = $latest_report->$attr;

                $prev_report = $latest_report;
            }

            if ($is_accum) {
                return [
                    'stat' => [
                        'value' => $accum_value,
                        'is_accumulated' => $is_accum
                    ],
                    'graph' => $graph_data
                ];
            } else {
                return [
                    'stat' => [
                        'value' => $latest_report->$attr,
                        'variation' => $accum_variation,
                        'is_accumulated' => $is_accum
                    ],
                    'graph' => $graph_data
                ];
            }
        }
    }
}

