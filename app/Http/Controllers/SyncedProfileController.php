<?php

namespace App\Http\Controllers;

use App\Tag;
use Illuminate\Http\Request;

class SyncedProfileController extends Controller {
    public function addTag(Request $r) {
        Tag::create([
            'synced_profile_id' => $r->synced_profile_id,
            'tag' => Tag::cleanWords($r->tag)
        ]);
    }

    public function deleteTag(Request $r) {
        Tag::where([
            ['synced_profile_id', $r->synced_profile_id],
            ['tag', Tag::cleanWords($r->tag)]
        ])->delete();
    }

    public function updateWords(Request $r) {
        Tag::where([
            'synced_profile_id' => $r->synced_profile_id,
            'tag' => Tag::cleanWords($r->tag)
        ])->update(['words' => '/' . implode("|", Tag::cleanWords($r->words)) . '/']);
    }

    public function updateRegexes(Request $r) {
        $formatted_regexes = $r->regexes != null ? '/' . implode("|", Tag::cleanWords($r->regexes)) . '/' : null;

        Tag::where([
            'synced_profile_id' => $r->synced_profile_id,
            'tag' => Tag::cleanWords($r->tag)
        ])->update(['regexes' => $formatted_regexes]);
    }
}
