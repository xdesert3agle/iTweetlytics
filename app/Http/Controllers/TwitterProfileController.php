<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class TwitterProfileController extends Controller {
    public function addTag(Request $r) {
        Tag::create([
            'synced_profile_id' => $r->synced_profile_id,
            'tag' => $r->tag
        ]);
    }

    public function deleteTag(Request $r) {
        Tag::where([
            ['synced_profile_id', $r->synced_profile_id],
            ['tag', $r->tag]
        ])->delete();
    }

    public function updateWords(Request $r) {
        Tag::where([
            'synced_profile_id' => $r->synced_profile_id,
            'tag' => $r->tag
        ])->update(['words' => implode(", ", $r->words)]);
    }
}
