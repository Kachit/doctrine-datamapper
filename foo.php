<?php
require 'vendor/autoload.php';

$config = new Doctrine\DBAL\Configuration();
$params = array(
    'dbname' => 'app_galaxygirl',
    'user' => 'root',
    'password' => '',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
);
$connection = Doctrine\DBAL\DriverManager::getConnection($params, $config);

$gateway = new Kachit\Silex\Database\Tests\Testable\Gateway($connection);

var_dump($gateway->createEmptyRow());