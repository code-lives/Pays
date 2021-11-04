# 小程序集合（支付、手机号解密、获取Token、支付异步通知、退款、订单查询）
## 开发进行中    【2021-11-4】
# 目录
- [小程序集合（支付、手机号解密、获取Token、支付异步通知、退款、订单查询）](#小程序集合支付手机号解密获取token支付异步通知退款订单查询)
  - [开发进行中    【2021-11-4】](#开发进行中----2021-11-4)
- [目录](#目录)
- [安装说明](#安装说明)
- [预下单](#预下单)
- [百度小程序](#百度小程序)
    - [Config](#config)
    - [token](#token)
    - [openid](#openid)
    - [解密手机号](#解密手机号)
    - [百度订单查询](#百度订单查询)
    - [百度退款](#百度退款)
    - [异步通知](#异步通知)
# 安装说明
    php > 5.3
# 预下单
```php

    $payName='Baidu';//设置驱动
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getParam();
    
```
# 百度小程序
### Config
 | 参数名字     | 类型   | 必须 | 说明                                   |
 | ------------ | ------ | ---- | -------------------------------------- |
 | appkey       | string | 是   | 百度小程序appkey                       |
 | payappKey    | string | 是   | 百度小程序支付appkey                   |
 | appSecret    | string | 是   | 百度小程序aapSecret                    |
 | dealId       | int    | 是   | 百度小程序支付凭证                     |
 | isSkipAudit  | int    | 是   | 默认为0； 0：不跳过开发者业务方审核；1：跳过开发者业务方审核。                     |
 | rsaPriKeyStr | string | 是   | 私钥（只需要一行长串，不需要文件）     |
 | rsaPubKeyStr | string | 是   | 百度小程序支付的平台公钥(支付回调需要) |

### token
```php

    $payName='Baidu';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
    //成功 array
    //失败 false
```
 | 返回参数     | 类型   | 必须 | 说明                                   |
 | ------------ | ------ | ---- | -------------------------------------- |
 | expires_in    | string | 是   | 凭证有效时间，单位：秒                   |
 | session_key    | string | 是   | session_key                  |
 | access_token       | string    | 是   | 获取到的凭证                     |



### openid

```php

    $payName='Baidu';//设置驱动
    $code="";
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
    //成功 array
    //失败 false
```
 | 返回参数     | 类型   | 必须 | 说明                                   |
 | ------------ | ------ | ---- | -------------------------------------- |
 | session_key    | string | 是   | session_key                  |
 | openid       | string    | 是   | 用户openid                     |


### 解密手机号

```php

    $payName='Baidu';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $ciphertext);
    echo $phone['mobile'];
    // 成功 array
    // 失败 false
```


### 百度订单查询
 | 参数名字     | 类型   | 必须 | 说明                |
 | ------------ | ------ | ---- | ------------------- |
 | access_token | string | 是   | 根据上面的获取token |
 | tpOrderId    | string | 是   | 平台订单号          |

```php

    $payName='Baidu';//设置驱动
    $Baidu = \Applet\Pay\Factory::getInstance('Baidu')->init($config);
    $order = [
            'tpOrderId' => '',//订单号
            'access_token' => $Baidu->getToken()['access_token'],
        ];
    $data = $Baidu->findOrder($order);
    // 成功 array 【自己看手册】
    // 失败 false
```


### 百度退款
 | 参数名字         | 类型   | 必须 | 说明                                                                                               |
 | ---------------- | ------ | ---- | -------------------------------------------------------------------------------------------------- |
 | token            | string | 是   | 根据上面的获取token                                                                                |
 | bizRefundBatchId | int    | 是   | 百度平台的订单号                                                                                   |
 | isSkipAudit      | int    | 是   | 默认为0； 0：不跳过开发者业务方审核；1：跳过开发者业务方审核。                                     |
 | orderId          | int    | 是   | 百度平台的订单号                                                                                   |
 | refundReason     | string | 是   | 退款描述                                                                                           |
 | refundType       | int    | 是   | 退款类型 1：用户发起退款；2：开发者业务方客服退款；3：开发者服务异常退款。百度小程序支付的平台公钥 |
 | tpOrderId        | string | 是   | 自己平台订单号                                                                                     |
 | userId           | int    | 是   | 用户uid（不是自己平台uid）                                                                         |
```php

    $order = [
	'token' => 'abcd',
	'bizRefundBatchId' => 123456,//百度平台订单号
	'isSkipAudit' => 1,
	'orderId' => 123456,
	'refundReason' => '测试退款',
	'refundType' => 2,//
	'tpOrderId' => '123',//自己平台订单号
	'userId' => 123,
    ];
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
    //返回 true false
```

### 异步通知
```php

    $data= \\Applet\Pay\Factory::getInstance($PayName)->init($config)->notifyCheck($_POST);
    //返回 true false
     
```
