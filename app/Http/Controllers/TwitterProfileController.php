<?php

namespace App\Http\Controllers;

use App\TwitterProfilesTags;
use Illuminate\Http\Request;

class TwitterProfileController extends Controller {
    public function addTag(Request $r) {
        TwitterProfilesTags::create([
            'twitter_profile_id' => $r->twitter_profile_id,
            'tag' => $r->tag
        ]);
    }

    public function deleteTag(Request $r) {
        TwitterProfilesTags::where([
            ['twitter_profile_id', $r->twitter_profile_id],
            ['tag', $r->tag]
        ])->delete();
    }

    public function updateWords(Request $r) {
        TwitterProfilesTags::where([
            'twitter_profile_id' => $r->twitter_profile_id,
            'tag' => $r->tag
        ])->update(['words' => implode(", ", $r->words)]);
    }
}
