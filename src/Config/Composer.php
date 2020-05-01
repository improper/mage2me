<?php

namespace App\Config;

class Composer
{
    public static function load()
    {
        return [
            'magento' => [
                'repo' => [
                    'host' => 'repo.magento.com',
                    'packages' => 'packages.json'
                ],
                'Open Source' => [
                    'package' => 'magento/project-community-edition'
                ],
                'Commerce' => [
                    'package' => 'magento/project-enterprise-edition'
                ]
            ]
        ];
    }
}

