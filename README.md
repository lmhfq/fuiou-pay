## Installing

```shell
$ composer require lmh/fuiou-pay -vvv
```

## Usage

### 收银台

```
 $app = Factory::cashier($config);
 $result= $app->transaction->pay($data);
 
```

### 聚合前置支付

```
 $app = Factory::prepare($config);
 $result= $app->transaction->pay($data);
 
```