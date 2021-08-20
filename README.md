Simple datamapper
===========
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)
[![Build Status](https://travis-ci.org/Kachit/doctrine-datamapper.svg?branch=master)](https://travis-ci.org/Kachit/doctrine-datamapper)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/64f75e92b17a458b9c8f0532fc1a8069)](https://www.codacy.com/app/antoxachaos/doctrine-datamapper?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=Kachit/doctrine-datamapper&amp;utm_campaign=Badge_Grade)
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
<?php
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
