<?php

namespace App\CommandTraits;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait GithubAccessTraits
 *
 * @package App\CommandTraits
 */
trait GithubAccessTraits
{
    /**
     * @return Command
     */
    public function addOptionNotifyGithub()
    {
        return $this->addOption(
            $this->keyNotifyGithub(),
            null,
            InputOption::VALUE_NONE,
            'Show token generator helper output'
        );
    }

    /**
     * @return Command
     */
    public function addOptionGithubRateToken()
    {
        return $this->addOption(
            $this->keyGithubToken(),
            null,
            InputOption::VALUE_REQUIRED,
            'GitHub access token. To prevent Github rate limiting, this input is required.'
        );
    }

    /**
     * @return string
     */
    protected function keyNotifyGithub()
    {
        return 'notify-github';
    }

    /**
     * @return string
     */
    protected function keyGithubToken()
    {
        return 'github-token';
    }
}
