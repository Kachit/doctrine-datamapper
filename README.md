Simple datamapper
===========
[![License](https://poser.pugx.org/leaphly/cart-bundle/license.svg)](https://packagist.org/packages/leaphly/cart-bundle)

Simple datamapper powered by doctrine2

```php
$gateway = new Gateway($doctrineConnection);

$row = $gateway->fetchByPk($id);

$rows = $gateway->fetchAll();
```
