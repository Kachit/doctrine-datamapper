# Simple database layer
Simple database layer powered by doctrine2

```
$gateway = new Gateway($doctrineConnection);

$row = $gateway->fetchByPk($id);

$rows = $gateway->fetchAll();
```
