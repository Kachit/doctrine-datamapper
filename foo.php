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

class Foo extends \Kachit\Silex\Database\Entity {

    protected $id;

    /**
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->id;
    }
}

$mapper = new \Kachit\Silex\Database\Mapper($gateway, new \Kachit\Silex\Database\Hydrator('Foo'));

$collection = $mapper->fetchAll();

var_dump($collection);