<?php

namespace App\CommandTraits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

trait MagentoAccessTraits
{
    /**
     * @return Command
     */
    public function addOptionNotifyMagento()
    {
        return $this->addOption(
            $this->keyNotifyMagento(),
            null,
            InputOption::VALUE_NONE,
            'Show token generator helper output'
        );
    }

    /**
     * @return Command
     */
    public function addOptionMagentoPublicKey()
    {
        return $this->addOption(
            $this->keyMagentoAccessPublic(),
            null,
            InputOption::VALUE_REQUIRED,
            'Public access key'
        );
    }

    /**
     * @return Command
     */
    public function addOptionMagentoPrivateKey()
    {
        return $this->addOption(
            $this->keyMagentoAccessPrivate(),
            null,
            InputOption::VALUE_REQUIRED,
            'Private access key'
        );
    }

    /**
     * @return string
     */
    protected function keyNotifyMagento()
    {
        return 'notify-magento';
    }

    /**
     * @return string
     */
    protected function keyMagentoAccessPublic()
    {
        return 'mage-access-key-public';
    }

    /**
     * @return string
     */
    protected function keyMagentoAccessPrivate()
    {
        return 'mage-access-key-private';
    }
}
