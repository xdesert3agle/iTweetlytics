<?php

namespace App\Console\Commands;

use App\Jobs\UpdateFollowersList;
use App\TwitterProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateFollowers extends Command {
    const REQUEST_WINDOW = 15;
    const MAX_CONSECUTIVE_REQUESTS = 15;
    const FOLLOWERS_PER_REQUEST = 5000;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followers:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recopila la lista de followers de todos los perfiles de la web para el día actual.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $this->addToLog('Comenzando el fetcheo');

        $allProfiles = TwitterProfile::all();

        $this->addToLog('Recogidos todos los twitter profiles');

        foreach ($allProfiles as $i => $profile) {

            $this->addToLog("$i: $profile->screen_name");

            // Se calcula el número de peticiones necesarias para poder fetchear la lista completa de followers del perfil
            $neededJobs = $profile->followers_count / (self::MAX_CONSECUTIVE_REQUESTS * self::FOLLOWERS_PER_REQUEST);

            $this->addToLog("Se van a necesitar " . ceil($neededJobs) . " jobs.");

            // Se mandan los jobs necesarios, con suficiente espacio entre ellos para no llegar al Rate Limit
            for ($j = 0; $j < ceil($neededJobs); $j++) {
                UpdateFollowersList::dispatch($profile)->delay(now()->addMinutes($j * self::REQUEST_WINDOW)->addSeconds(10));
            }
        }
    }

    protected function addToLog($string) {
        $file = Storage::get('file.txt');
        Storage::put('file.txt', $file . "$string\n");
    }
}
