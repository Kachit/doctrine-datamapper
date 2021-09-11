Simple doctrine datamapper
===========
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)
[![Build Status](https://app.travis-ci.com/Kachit/doctrine-datamapper.svg?branch=master)](https://travis-ci.com/Kachit/doctrine-datamapper)
[![codecov](https://codecov.io/gh/Kachit/doctrine-datamapper/branch/master/graph/badge.svg?token=SVNIII4H2W)](https://codecov.io/gh/Kachit/doctrine-datamapper)
[![Latest Stable Version](https://poser.pugx.org/kachit/doctrine-datamapper/v/stable)](https://packagist.org/packages/kachit/doctrine-datamapper)
[![Total Downloads](https://poser.pugx.org/kachit/doctrine-datamapper/downloads)](https://packagist.org/packages/kachit/doctrine-datamapper)

Simple datamapper powered by doctrine2

```php
<?php
//create database connection
$params = [];
$connection = Doctrine\DBAL\DriverManager::getConnection($params);
```

```php
<?php
//create your table gateway
$gateway = new Your\Project\Models\TableGateway($connection);

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
<?php
//create mapper
$entity = new Your\Project\Models\Entity();
$mapper = new Your\Project\Models\Mapper($gateway, $entity);

//fetch by PK
$entity = $mapper->fetchByPk(1);

//fetch all without filter
$collection = $mapper->fetchAll();

//fetch list with filter (all active)
$filter = new Kachit\Database\Query\Filter();
$filter->createCondition('active', true);
$collection = $mapper->fetchAll($filter);
```
