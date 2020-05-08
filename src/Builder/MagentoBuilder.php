<?php

namespace App\Builder;

use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class MagentoBuilder
 *
 * @package App\Builder
 */
class MagentoBuilder
{
    /**
     * @param  \Closure $processOutput       Handle client output
     * @param  string   $edition
     * @param  string   $version
     * @param  string   $outDir
     * @param  string   $magentoToken
     * @param  string   $magentoPrivateToken
     * @param  string   $githubToken
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function deploy(
        $processOutput,
        $edition = 'Open Source',
        $version = '2.3.5',
        $outDir = 'magento-store',
        $magentoToken = "",
        $magentoPrivateToken = "",
        $githubToken = ""
    ) {
        $composerAuth = sprintf(
            '{"http-basic": {"repo.magento.com": {"username": "%s","password": "%s"}}, "github-oauth": {"github.com": "%s"}}',
            $magentoToken,
            $magentoPrivateToken,
            $githubToken
        );

        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists(self::temporaryPath());
        $filesystem->ensureDirectoryExists($outDir);
        $filesystem->cleanDirectory(self::temporaryPath());

        $cmdInstall = 'composer create-project -n --no-install --repository=https://repo.magento.com/ ' . $edition . ' . ' . $version;
        $createProject = Process::fromShellCommandline(
            $cmdInstall,
            self::temporaryPath(),
            [
                'COMPOSER_AUTH' => $composerAuth
            ]
        );

        $createProject->start(
            function ($type, $buffer) use ($processOutput) {
                $processOutput($type, $buffer);
            }
        );
        $createProject->wait();
        if ($createProject->getExitCode() !== 0) {
            throw new ProcessFailedException($createProject);
        }

        $filesystem->copyDirectory(self::temporaryPath(), $outDir);
        self::putSampleNginx($version, $filesystem, $outDir);
        $filesystem->put($outDir . '/auth.json', $composerAuth);
    }

    /**
     * @return string
     */
    protected static function temporaryPath()
    {
        return sys_get_temp_dir() . '/magento';
    }

    /**
     * @param  $version
     * @param  Filesystem $filesystem
     * @param  $outDir
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected static function putSampleNginx($version, Filesystem $filesystem, $outDir)
    {
        $mageRepoClient = HttpClient::createForBaseUri('https://raw.githubusercontent.com/magento/magento2/');
        $repoVersion = self::getComposerData($filesystem, $outDir)['version'];
        $sampleNginx = $mageRepoClient->request('GET', $repoVersion . '/nginx.conf.sample')->getContent(true);
        $filesystem->put($outDir . '/nginx.conf.sample', $sampleNginx, true);
    }
}
