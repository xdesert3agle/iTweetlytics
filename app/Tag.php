<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
    protected $guarded = [];

    public static function parseWordsToString($tags) {
        $words = [];

        if (!$tags instanceof Collection)
            $tags = $tags->get();

        foreach ($tags as $tag) {
            $words[] = explode(", ", $tag->words);
        }

        return $words;
    }
}
