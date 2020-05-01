<?php

namespace App\Config;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

class Translations {
    public static function load($locale = 'en') {
        return new Translator(new ArrayLoader(), $locale);
    }
}
