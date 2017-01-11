<?php
require_once 'vendor/autoload.php';

use Stubs\DB\Gateway as UsersGateway;
use Kachit\Database\Query\Filter;
use Kachit\Database\Mapper;
use Kachit\Database\Gateway;
use Stubs\DB\Entity as User;

$params = [
    'dbname' => 'phalcondb',
    'user' => 'phalcon',
    'password' => 'secret',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
];

$config = new Doctrine\DBAL\Configuration();
$connection = Doctrine\DBAL\DriverManager::getConnection($params, $config);

$gateway = new UsersGateway($connection);

//Fetch all
//var_dump($gateway->fetchAll());

//Fetch all by filter
$filter = new Filter();
$filter->createCondition('id', [1, 3], 'IN');
$filter->createCondition('name', '%foo%', 'LIKE');
//var_dump($gateway->fetchAll($filter));

//Fetch by PK
//var_dump($gateway->fetchByPk(11));

//Fetch one by condition
$filter = new Filter();
$filter->createCondition('id', 1);
//var_dump($gateway->fetch($filter));

//Fetch column
$filter = new Filter();
$filter->createCondition('id', 10);
//var_dump($gateway->fetchColumn('name', $filter));

//Count
//var_dump($gateway->count());

//Insert
//var_dump($gateway->insert(['name' => uniqid(), 'email' => uniqid()]));

//Update
//var_dump($gateway->updateByPk(['active' => 0, 'qwer' => 123], 8));

//Delete
$filter = new Filter();
$filter->createCondition('id', 8);
//var_dump($gateway->delete($filter));

$mapper = new Mapper($gateway, new User());
/* @var User $user */
$user = $mapper->fetchByPk(5);
//var_dump($user);
//$user->setActive(0);

//$mapper->delete($user);
var_dump($mapper->fetchAll());
