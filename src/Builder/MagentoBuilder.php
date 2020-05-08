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
     * @param \Closure $processOutput Handle client output
     * @param string $edition
     * @param string $version
     * @param string $outDir
     * @param string $magentoToken
     * @param string $magentoPrivateToken
     * @param string $githubToken
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
    )
    {
        $composerAuth = self::makeComposerAuth($magentoToken, $magentoPrivateToken, $githubToken);

        self::prepareDirectoriesForInstall($outDir);
        self::composerCreateProject($edition, $version, $composerAuth, $processOutput);
        self::moveToOutDirectory($outDir);
        self::saveComposerAuth($outDir, $composerAuth);
        self::putSampleNginx($outDir, $version);
    }

    /**
     * @param $magentoToken
     * @param $magentoPrivateToken
     * @param $githubToken
     * @return string
     */
    private static function makeComposerAuth($magentoToken, $magentoPrivateToken, $githubToken)
    {
        return sprintf(
            '{"http-basic": {"repo.magento.com": {"username": "%s","password": "%s"}}, "github-oauth": {"github.com": "%s"}}',
            $magentoToken,
            $magentoPrivateToken,
            $githubToken
        );
    }

    /**
     * @param $outDir
     * @return void
     */
    private static function prepareDirectoriesForInstall($outDir)
    {
        $filesystem = new Filesystem();
        $filesystem->ensureDirectoryExists(self::temporaryPath());
        $filesystem->ensureDirectoryExists($outDir);
        $filesystem->cleanDirectory(self::temporaryPath());
    }

    /**
     * @return string
     */
    private static function temporaryPath()
    {
        return sys_get_temp_dir() . '/magento';
    }

    /**
     * @param $edition
     * @param $version
     * @param $composerAuth
     * @param \Closure $processOutput
     */
    private static function composerCreateProject($edition, $version, $composerAuth, \Closure $processOutput)
    {
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
    }

    /**
     * @param $outDir
     * @return void
     */
    private static function moveToOutDirectory($outDir)
    {
        $filesystem = new Filesystem();
        $filesystem->copyDirectory(self::temporaryPath(), $outDir);
    }

    /**
     * This is appropriate for ensuring Magento's NGINX is in place before a composer install has been executed
     * @param  $outDir
     * @param $mageVersion
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private static function putSampleNginx($outDir, $mageVersion)
    {
        $filesystem = new Filesystem();
        $mageRepoClient = HttpClient::createForBaseUri('https://raw.githubusercontent.com/magento/magento2/');
        $installedVersion = @self::getComposerData($filesystem, $outDir)['version'];
        if(empty($installedVersion)){
            $installedVersion = $mageVersion;
        }
        $sampleNginx = $mageRepoClient->request('GET', $installedVersion . '/nginx.conf.sample')->getContent(true);
        $filesystem->put($outDir . '/nginx.conf.sample', $sampleNginx, true);
    }

    /**
     * @param $outDir
     * @param $composerAuth
     * @return void
     */
    private static function saveComposerAuth($outDir, $composerAuth)
    {
        (new Filesystem())->put($outDir . '/auth.json', $composerAuth);
    }

    /**
     * @param $processOutput
     * @param string $version
     * @param string $outDir
     * @param string $githubUrl
     * @param string $magentoToken
     * @param string $magentoPrivateToken
     * @param string $githubToken
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function deployFromGitHub(
        $processOutput,
        $version = '2.3',
        $outDir = 'new-magento',
        $githubUrl = 'https://github.com/magento/magento2',
        $magentoToken = "",
        $magentoPrivateToken = "",
        $githubToken = ""
    )
    {
        $composerAuth = self::makeComposerAuth($magentoToken, $magentoPrivateToken, $githubToken);
        self::prepareDirectoriesForInstall($outDir);
        self::gitClone($version, self::authenticatedRepoUrl($githubToken, $githubUrl), $processOutput);
        self::moveToOutDirectory($outDir);
        self::saveComposerAuth($outDir, $composerAuth);
    }

    /**
     * @param $branchOrTag
     * @param $githubRepo
     * @param \Closure $processOutput
     */
    private static function gitClone($branchOrTag, $githubRepo, \Closure $processOutput)
    {
        $cmdGitClone = 'git clone --depth 1 --single-branch --branch ' . $branchOrTag . ' ' . $githubRepo . ' . ';

        $gitClone = Process::fromShellCommandline(
            $cmdGitClone,
            self::temporaryPath()
        );

        $gitClone->start(
            function ($type, $buffer) use ($processOutput) {
                $processOutput($type, $buffer);
            }
        );
        $gitClone->wait();
        if ($gitClone->getExitCode() !== 0) {
            throw new ProcessFailedException($gitClone);
        }
    }

    /**
     * @param $githubToken
     * @param $githubUrl
     * @return string|string[]
     */
    private static function authenticatedRepoUrl($githubToken, $githubUrl)
    {
        $cloneUrl = rtrim(rtrim($githubUrl, '/'), '.git') . '.git';
        return str_replace(
            'https://',
            'https://' . $githubToken . '@',
            $cloneUrl
        );
    }

    /**
     * @param Filesystem $filesystem
     * @param $outDir
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private static function getComposerData(Filesystem $filesystem, $outDir)
    {
        return json_decode($filesystem->get($outDir . DIRECTORY_SEPARATOR . 'composer.json'), true);
    }
}
