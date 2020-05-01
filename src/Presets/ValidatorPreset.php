<?php

namespace App\Presets;

use App\Config\Translations;
use Illuminate\Validation\Validator;

class ValidatorPreset
{
    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = []) {
        return new Validator(Translations::load(), $data, $rules, $messages, $customAttributes);
    }
}
