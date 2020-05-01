<?php

namespace App\Config;

class Github
{
    public static function load()
    {
        return [
            'user' => [
                'auth_url' => 'https://api.github.com/user'
            ]
        ];
    }
}

