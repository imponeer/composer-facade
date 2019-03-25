<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 9/03/2019
 * Time: 18:50
 */

use Imponeer\Composer\composerfacade;

include '../vendor/autoload.php';
$nullio = new \Composer\IO\NullIO();
$composerapp = new composerfacade($nullio, '../composer.json');
echo $composerapp->generate_autoload();
