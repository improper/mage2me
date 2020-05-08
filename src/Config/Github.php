<?php

namespace App\Config;

/**
 * Class Github
 *
 * @package App\Config
 */
class Github
{
    /**
     * @return array
     */
    public static function load()
    {
        return [
            'user' => [
                'auth_url' => 'https://api.github.com/user'
            ],
            'magento'=> [
                'Open Source' => 'https://github.com/magento/magento2/'
            ],
        ];
    }
}
