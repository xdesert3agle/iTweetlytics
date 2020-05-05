<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScheduledTweet extends Model {
    protected $appends = ['formatted_schedule_time', 'schedule_hour'];

    public function getFormattedScheduleTimeAttribute() {
        return Carbon::createFromTimestamp($this->schedule_time / 1000)->format('d-m-Y H:i:s');
    }

    public function getScheduleHourAttribute() {
        return Carbon::createFromTimestamp($this->schedule_time / 1000)->format('H:i:s');
    }
}
