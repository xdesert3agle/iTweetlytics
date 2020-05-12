<?php

namespace App\Http\Controllers;

use App\Befriend;
use App\Follow;
use App\Follower;
use App\Friend;
use App\Report;
use App\TwitterProfile;
use App\Unfollow;
use App\Unfriend;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TestController extends Controller {

    public function test() {
        setlocale(LC_TIME, '');
        $profileId = 286561116;
        $stat = "followers";
        $timeInterval = "weekly";



        // Intervalos de tiempo
        $startTime = "";
        $now = Carbon::now();
        $weekAgo = Carbon::now()->subWeek()->startOfDay();
        $monthAgo = Carbon::now()->subMonth()->startOfDay();
        $yearAgo = Carbon::now()->subYear()->startOfDay();

        $isUserOwnerOfProfile = TwitterProfile::find($profileId)->belongsToUser(Auth::id());

        $attr = ""; // Campo del modelo Report que se va a obtener
        $is_accum = false; // ¿El campo es acumulado sobre el tiempo?
        $target_model = "";

        if (true) {
            switch ($stat) {
                case 'followers':
                    $attr = 'total_followers';
                    $is_accum = false;
                    $target_model = Follower::class;
                    break;

                case 'follows':
                    $attr = 'follows';
                    $is_accum = true;
                    $target_model = Follow::class;
                    break;

                case 'unfollows':
                    $attr = 'unfollows';
                    $is_accum = true;
                    $target_model = Unfollow::class;
                    break;

                case 'friends':
                    $attr = 'total_friends';
                    $is_accum = false;
                    $target_model = Friend::class;
                    break;

                case 'befriends':
                    $attr = 'befriends';
                    $is_accum = true;
                    $target_model = Befriend::class;
                    break;

                case 'unfriends':
                    $attr = 'unfriends';
                    $is_accum = true;
                    $target_model = Unfriend::class;
                    break;

                case 'f2f_ratio':
                    $attr = 'friends_to_followers_ratio';
                    $is_accum = false;
                    $target_model = null;
                    break;
            }

            $group_by_format = ""; // ¿Cömo se agrupan los datos de la gráfica?

            switch ($timeInterval) {
                case 'weekly':
                    $startTime = $weekAgo;
                    $group_by_format = "%d/%m";
                    break;

                case 'monthly':
                    $startTime = $monthAgo;
                    $group_by_format = "%d";
                    break;

                case 'yearly':
                    $startTime = $yearAgo;
                    $group_by_format = "%B";

                    break;
            }

            $reports = Report::whereBetween('created_at', [$startTime, $now])
                ->where('twitter_profile_id', $profileId)
                ->get()
                ->groupBy(function ($val) use ($group_by_format) {
                    return Carbon::parse($val->created_at)->formatLocalized($group_by_format);
                });

            $graph_data = [];
            $latest_report = "";
            $accum_variation = 0;
            $accum_value = 0;

            $prev_report = (object)array_values($reports->toArray())[0][0]; // Al principio, el report anterior es el primero

            foreach ($reports as $i => $report) {
                $latest_report_index = count($report) - 1;
                $latest_report = (object)$report[$latest_report_index]->toArray();

                $accum_value += $latest_report->$attr;
                $current_variation = $latest_report->$attr - $prev_report->$attr;
                $accum_variation += $current_variation;

                $graph_data[$i] = $latest_report->$attr;

                $prev_report = $latest_report;
            }

            $response = [];

            // Si se escoge modelo target es porque la estadística objetivo va a requerir de una lista de usuarios
            if ($target_model != null) {

                // Si es acumulado significa que la lista va a estar acotada sobre un periodo de tiempo
                $users_list = $target_model::where('twitter_profile_id', $profileId)
                    ->when($is_accum, function ($query) use ($startTime, $now) {
                        $query->whereBetween('created_at', [$startTime, $now]);
                    })
                    ->orderByDesc('id')
                    ->get();

                $response['users_list'] = $users_list->toArray();
            }

            if ($is_accum) {
                dd(array_merge($response, [
                    'stat' => [
                        'value' => $accum_value,
                        'is_accumulated' => $is_accum
                    ],
                    'graph' => $graph_data,
                ]));
            } else {
                dd(array_merge($response, [
                    'stat' => [
                        'value' => $latest_report->$attr,
                        'variation' => $accum_variation,
                        'is_accumulated' => $is_accum
                    ],
                    'graph' => $graph_data,
                ]));
            }
        }
    }
}
