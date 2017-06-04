Simple datamapper
===========
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)
[![Build Status](https://travis-ci.org/Kachit/doctrine-datamapper.svg?branch=master)](https://travis-ci.org/Kachit/doctrine-datamapper)

Simple datamapper powered by doctrine2

```php
$gateway = new Gateway($doctrineConnection);

$row = $gateway->fetchByPk($id);

$rows = $gateway->fetchAll();
```
