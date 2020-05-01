<?php

namespace App\Presets;

use App\Config\Translations;
use Illuminate\Validation\Validator;

/**
 * Class ValidatorPreset
 *
 * @package App\Presets
 */
class ValidatorPreset
{
    /**
     * @param  array $data
     * @param  array $rules
     * @param  array $messages
     * @param  array $customAttributes
     * @return Validator
     */
    public static function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        return new Validator(Translations::load(), $data, $rules, $messages, $customAttributes);
    }
}
