<?php

namespace App\Config;

use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;

/**
 * Class Translations
 *
 * @package App\Config
 */
class Translations
{
    /**
     * @param  string $locale
     * @return Translator
     */
    public static function load($locale = 'en')
    {
        return new Translator(new ArrayLoader(), $locale);
    }
}
