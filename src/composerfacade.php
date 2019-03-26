<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 1/03/2019
 * Time: 8:24
 */

namespace Imponeer\Composer;

use Composer\Command\BaseCommand;
use Composer\Factory;
use Composer\Cache;
use Composer\Installer;
use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;

/**
 * Class composer_facade
 * @package Imponeer\Composer
 *
 * provide a PHP interface with a composer library installed in your solution.
 * functionality provided : install a new package, rebuild the autoloader, remove a package, list installed packages,
 * list packages with an update available, update package, find a package
 *
 */
class composerfacade
{
    protected $homepath;
    protected $composer;
    var $installationManager;
    var $localRepo;
    var $package;
    var $config;

    var $io;

    /**
     * composer_facade constructor.
     * @param $homepath the composer home folder where the composer.json and composer.lock file are situated
     */
    public function __construct(\Composer\IO\IOInterface $iolocal, $homepath)
    {
        echo "starting the composer factory creation!\n";
        try {
            $this->io = new \Composer\IO\NullIO();
            $composerapp = Factory::create($this->io, $homepath, false);
        } catch (\InvalidArgumentException $e) {
            if (true) {
                $this->io->writeError($e->getMessage());
                exit(1);
            }
        } catch (JsonValidationException $e) {
            $errors = ' - ' . implode(PHP_EOL . ' - ', $e->getErrors());
            $message = $e->getMessage() . ':' . PHP_EOL . $errors;
            throw new JsonValidationException($message);
        }
        echo "getting installation manager\n";
        $this->installationManager = $composerapp->getInstallationManager();
        echo "getting repository manager\n";
        $this->localRepo = $composerapp->getRepositoryManager()->getLocalRepository();
        echo "getting package\n";
        $this->package = $composerapp->getPackage();
        echo "getting config\n";
        $this->config = $composerapp->getConfig();
        $this->composer = $composerapp;
    }

        /**
         * install the composer packages
         */
    public function install()
    {
        $command = 'install';
        $this->run($command);
    }

    public function clearcache()
    {
        $config = Factory::createConfig();

        $cachePaths = array(
            'cache-vcs-dir' => $config->get('cache-vcs-dir'),
            'cache-repo-dir' => $config->get('cache-repo-dir'),
            'cache-files-dir' => $config->get('cache-files-dir'),
            'cache-dir' => $config->get('cache-dir'),
        );

        foreach ($cachePaths as $key => $cachePath) {
            $cachePath = realpath($cachePath);
            if (!$cachePath) {
                $this->io->writeError("<info>Cache directory does not exist ($key): $cachePath</info>");

                continue;
            }
            $cache = new Cache($this->io, $cachePath);
            if (!$cache->isEnabled()) {
                $this->io->writeError("<info>Cache is not enabled ($key): $cachePath</info>");

                continue;
            }

            echo "<info>Clearing cache ($key): $cachePath</info>";
            $cache->clear();
        }
    }
    public function addPackage()
    {
        echo "getting composer file";
        $configfile = Factory::getComposerFile();
        $json = new JsonFile($configfile);

        $newlyCreated = !file_exists($configfile);
        if ($newlyCreated && !file_put_contents($configfile, "{\n}\n")) {
            echo '<error>'.$configfile.' could not be created.</error>';

            return 1;
        }
        if (!is_readable($configfile)) {
            echo '<error>'.$configfile.' is not readable.</error>';

            return 1;
        }
        if (!is_writable($configfile)) {
            echo '<error>'.$configfile.' is not writable.</error>';

            return 1;
        }

        if (filesize($configfile) === 0) {
            file_put_contents($configfile, "{\n}\n");
        }

    }

        /**
         * fetch a list of packages that have a newer version available
         */
    public function update_list()
    {
        $command = 'outdated -D';
        $this->run($command);
    }

    public function validate()
    {
        $command = 'validate';
        $this->run($command);
    }

    /**
     * trigger the generation of the autoloader in composer
     */
    public function generate_autoload()
    {
        echo "generating autoloader...\n";
        $generator = $this->composer->getAutoloadGenerator();
        $generator->setDevMode(true);
        $generator->setClassMapAuthoritative(true);
        $generator->setApcu(true);
        $generator->setRunScripts(true);
        $numberOfClasses = $generator->dump($this->config, $this->localRepo, $this->package, $this->installationManager, 'composer', true);

        echo "number of classes autoloaded : " . $numberOfClasses;
        return $numberOfClasses;
    }


}