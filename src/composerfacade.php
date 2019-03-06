<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 1/03/2019
 * Time: 8:24
 */

namespace imponeer;

use Composer\Console\Application as ComposerApp;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class composer_facade
 * @package imponeer
 *
 * provide a PHP interface with a composer library installed in your solution.
 * functionality provided : install a new package, rebuild the autoloader, remove a package, list installed packages,
 * list packages with an update available, update package, find a package
 *
 */
class composer_facade
{
    private $composerapp;

    /**
     * composer_facade constructor.
     * @param $homepath the composer home folder where the composer.json and composer.lock file are situated
     */
public function __construct($homepath)
{
    // look for the composer.json and composer.lock file on this location
    putenv('COMPOSER_HOME='. $homepath);

    $composerapp = new ComposerApp();
    $composerapp->setAutoExit(false);
}

    /**
     * install the composer packages
     */
public function install()
{

}

    /**
     * fetch a list of packages that have a newer version available
     */
public function update_list()
{
    $command = 'outdated -D';
    run($command);
}

private function run($command)
{
    $command = new StringInput($command);
    $output = new BufferedOutput();

    $responsecode = $this->composerapp->run($command,$output);
return $output->fetch();
}
}