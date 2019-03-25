<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 1/03/2019
 * Time: 8:24
 */

namespace Imponeer\Composer;

use Composer\Command\BaseCommand;
//use Composer\Console\Application as ComposerApp;
//use Symfony\Component\Console\Input\StringInput;
//use Symfony\Component\Console\Output\BufferedOutput;

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
    var $composerapp;
    var $installationManager;
    var $localRepo;
    var $package;
    var $config;

    /**
     * composer_facade constructor.
     * @param $homepath the composer home folder where the composer.json and composer.lock file are situated
     */
    public function __construct(\Composer\IO\IOInterface $io, $homepath)
    {
        echo "starting the composer factory creation!\n";
        try {
            $composerapp = \Composer\Factory::create($this->io, $homepath, true);
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
        $installationManager = $composerapp->getInstallationManager();
        echo "getting repository manager\n";
        $localRepo = $composerapp->getRepositoryManager()->getLocalRepository();
        echo "getting package\n";
        $package = $composerapp->getPackage();
        echo "getting config\n";
        $config = $composerapp->getConfig();

        //return $composerapp;
    }

        /**
         * install the composer packages
         */
    public function install()
    {
        $command = 'install';
        $this->run($command);
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
        $generator = $this->composerapp->getAutoloadGenerator();
        $generator->setDevMode(true);
        $generator->setClassMapAuthoritative(true);
        $generator->setApcu(true);
        $generator->setRunScripts(true);
        $numberOfClasses = $generator->dump($config, $localRepo, $package, $installationManager, 'composer', true);

        echo "number of classes autoloaded : " . $numberOfClasses;
        return $numberOfClasses;
    }

    function run($command)
    {
        //$composerapp = new \Composer\Composer();
        // Uit Factory.php - maakt een geconfigureerde composer instance aan

    }
}