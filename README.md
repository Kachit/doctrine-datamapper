Simple datamapper
===========
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)
[![Build Status](https://travis-ci.org/Kachit/doctrine-datamapper.svg?branch=master)](https://travis-ci.org/Kachit/doctrine-datamapper)

Simple datamapper powered by doctrine2

```php
//create db connection
$params = []
$connection = new Doctrine\DBAL\Connection($params);
```

```php
//create your table gateway
$gateway = new Your\Project\Namespace\TableGateway($connection);

//fetch by PK
$row = $gateway->fetchByPk($id);

//fetch all without filter
$rows = $gateway->fetchAll();

//fetch list with filter (all active)
$filter = new Kachit\Database\Query\Filter();
$filter->createCondition('active', true);
$rows = $gateway->fetchAll($filter);
```

```php
//create mapper
$entity = new Your\Project\Namespace\Entity();
$mapper = new Your\Project\Namespace\Mapper($gateway, $entity);

//fetch by PK
$entity = $mapper->fetchByPk(1);

//fetch all without filter
$collection = $mapper->fetchAll();

//fetch list with filter (all active)
$filter = new Kachit\Database\Query\Filter();
$filter->createCondition('active', true);
$collection = $mapper->fetchAll($filter);
```
