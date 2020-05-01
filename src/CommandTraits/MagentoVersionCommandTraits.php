<?php

namespace App\CommandTraits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait MagentoVersionCommandTraits
 *
 * @package App\CommandTraits
 */
trait MagentoVersionCommandTraits
{
    /**
     * @return Command
     */
    public function addOptionMageEdition()
    {
        return $this->addOption(
            $this->keyMageEdition(),
            null,
            InputOption::VALUE_OPTIONAL,
            'Magento Edition. ["Open Source" || "Commerce"]',
            'Open Source'
        );
    }

    /**
     * @return Command
     */
    public function addOptionMageVersion()
    {
        return $this->addOption(
            $this->keyMageVersion(),
            null,
            InputOption::VALUE_OPTIONAL,
            'Magento Edition. [2.1, 2.2, 2.3.5, 2.4-develop, ...]',
            '2.3.5'
        );
    }

    /**
     * @return string
     */
    protected function keyMageEdition()
    {
        return 'mage-edition';
    }

    /**
     * @return string
     */
    protected function keyMageVersion()
    {
        return 'mage-version';
    }
}
