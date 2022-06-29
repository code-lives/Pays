# 微信小程序、字节小程序、百度小程序、快手小程序、微信 App、微信 H5（支付、手机号解密、获取 Token、支付异步通知、退款、订单查询）

# 目录、微信小程序、字节小程序、百度小程序、微信 App 支付

- [微信小程序、字节小程序、百度小程序、快手小程序、微信 App、微信 H5（支付、手机号解密、获取 Token、支付异步通知、退款、订单查询）](#微信小程序字节小程序百度小程序快手小程序微信-app微信-h5支付手机号解密获取-token支付异步通知退款订单查询)
- [目录、微信小程序、字节小程序、百度小程序、微信 App 支付](#目录微信小程序字节小程序百度小程序微信-app-支付)
- [安装说明](#安装说明)
- [功能支持](#功能支持)
- [预下单](#预下单)
- [百度小程序](#百度小程序)
    - [Config](#config)
    - [token](#token)
    - [openid](#openid)
    - [解密手机号](#解密手机号)
    - [百度订单查询](#百度订单查询)
    - [百度退款](#百度退款)
- [字节小程序](#字节小程序)
    - [Config](#config-1)
    - [token](#token-1)
    - [openid](#openid-1)
    - [解密手机号](#解密手机号-1)
    - [字节订单查询](#字节订单查询)
    - [字节分账](#字节分账)
    - [字节退款](#字节退款)
- [微信小程序](#微信小程序)
    - [Config](#config-2)
    - [token](#token-2)
    - [openid](#openid-2)
    - [微信解密手机号](#微信解密手机号)
    - [微信订单查询](#微信订单查询)
    - [微信退款](#微信退款)
- [快手小程序](#快手小程序)
    - [Config](#config-3)
    - [openid](#openid-3)
    - [快手解密手机号](#快手解密手机号)
    - [快手订单查询](#快手订单查询)
    - [快手退款](#快手退款)
- [微信 APP](#微信-app)
    - [Config](#config-4)
- [异步通知](#异步通知)
  - [字节](#字节)
  - [微信回调(通用微信 H5 支付、小程序、微信公众号) 记得改 config 配置](#微信回调通用微信-h5-支付小程序微信公众号-记得改-config-配置)
  - [百度小程序回调](#百度小程序回调)
  - [快手小程序](#快手小程序-1)
- [随便打赏](#随便打赏)

# 安装说明

    composer require code-lives/applet-pays 5.0


# 功能支持

| 第三方     | token | openid | 支付  | 回调  | 退款  | 订单查询 | 解密手机号 | 分账 |
| :----------: | :---: | :----: | :---: | :---: | :---: | :------: | :--------: | :--------: |
| 微信小程序 |   ✓   |   ✓    |   ✓   |   ✓   |   ✓   |    ✓     |     ✓      |     x     |
| 微信h5 |   x   |   x    |   ✓   |   ✓   |   ✓   |    ✓     |     x      |     x     |
| 微信公众号 |   x   |   x    |   ✓   |   ✓   |   ✓   |    ✓     |     x      |     x     |
| 百度小程序 |   ✓   |   ✓    |   ✓   |   ✓   |   ✓   |    ✓     |     ✓      |     x      |
| 字节小程序 |   ✓   |   ✓    |   ✓   |   ✓   |   ✓   |    ✓     |     ✓      |     ✓      |
| 快手小程序 |   ✓   |   ✓    |   ✓   |   ✓   |   ✓   |    ✓     |     ✓      |     ✓      |
| 微信APP |   x   |   ✓    |   ✓   |   ✓   |   ✓   |    ✓     |     x      |     x      |


# 预下单

```php

    $payName='Baidu';//百度
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getParam();

    $payName='Byte';//字节
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","描述")->getParam();

    $payName='Weixin';//微信
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","openid")->getParam();

    $payName='Weixin';//微信公众号【appid 和secret 换成公众号的】
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","openid")->getParam();

    $payName='Weixin';//微信H5【appid 和secret 换成公众号的】
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getH5Param();

    $payName='Weixin';//微信APP (没有openid)
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getParam();

    $payName='Kuaishou';//快手
    $pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述",'openid', 'access_token')->getParam();



```

# 百度小程序

### Config

| 参数名字     | 类型   | 必须 | 说明                                                            |
| ------------ | ------ | ---- | --------------------------------------------------------------- |
| appkey       | string | 是   | 百度小程序 appkey                                               |
| payappKey    | string | 是   | 百度小程序支付 appkey                                           |
| appSecret    | string | 是   | 百度小程序 aapSecret                                            |
| dealId       | int    | 是   | 百度小程序支付凭证                                              |
| isSkipAudit  | int    | 是   | 默认为 0； 0：不跳过开发者业务方审核；1：跳过开发者业务方审核。 |
| rsaPriKeyStr | string | 是   | 私钥（只需要一行长串，不需要文件）                              |
| rsaPubKeyStr | string | 是   | 百度小程序支付的平台公钥(支付回调需要)                          |

### token

```php

    $payName='Baidu';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
    //成功 array
    //失败 false
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| session_key  | string | 是   | session_key            |
| access_token | string | 是   | 获取到的凭证           |

### openid

```php

    $payName='Baidu';//设置驱动
    $code="";
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
    //成功 array
    //失败 false
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |

### 解密手机号

```php

    $payName='Baidu';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $ciphertext);
    echo $phone['mobile'];
    // 成功 array
    // 失败 false
```

### 百度订单查询

| 参数名字     | 类型   | 必须 | 说明                 |
| ------------ | ------ | ---- | -------------------- |
| access_token | string | 是   | 根据上面的获取 token |
| tpOrderId    | string | 是   | 平台订单号           |

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
| access_token     | string | 是   | 根据上面的获取 token                                                                               |
| bizRefundBatchId | int    | 是   | 百度平台的订单号                                                                                   |
| isSkipAudit      | int    | 是   | 默认为 0； 0：不跳过开发者业务方审核；1：跳过开发者业务方审核。                                    |
| orderId          | int    | 是   | 百度平台的订单号                                                                                   |
| refundReason     | string | 是   | 退款描述                                                                                           |
| refundType       | int    | 是   | 退款类型 1：用户发起退款；2：开发者业务方客服退款；3：开发者服务异常退款。百度小程序支付的平台公钥 |
| tpOrderId        | string | 是   | 自己平台订单号                                                                                     |
| userId           | int    | 是   | 用户 uid（不是自己平台 uid）                                                                       |

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

# 字节小程序

### Config

| 参数名字    | 类型   | 必须 | 说明                              |
| ----------- | ------ | ---- | --------------------------------- |
| token       | string | 是   | 担保交易回调的 Token(令牌)        |
| salt        | string | 是   | 担保交易的 SALT                   |
| merchant_id | string | 是   | 担保交易的商户号                  |
| app_id      | int    | 是   | 小程序的 APP_ID                   |
| secret      | string | 是   | 小程序的 APP_SECRET               |
| notify_url  | string | 是   | 支付回调 url                      |
| settle_url  | string | 否   | 分账回调 url,没有默认支付回调 url |

### token

```php

    $payName='Byte';//驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
    //成功 array
    //失败 false
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| access_token | string | 是   | 获取到的凭证           |

### openid

```php

    $payName='Byte';//设置驱动
    $code="";
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
    //成功 array
    //失败 false
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |
| unionid     | string | 是   | unionid     |

### 解密手机号

```php

    $payName='Baidu';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
    echo $phone['phoneNumber'];
    // 成功 array
    // 失败 false
```

### 字节订单查询

```php

    $payName='Byte';//设置驱动
    $Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
    $data = $Baidu->findOrder("订单号");
    // 成功 array 【自己看手册】
    // 失败 false
```

### 字节分账

| 参数名字      | 类型   | 必须 | 说明                           |
| ------------- | ------ | ---- | ------------------------------ |
| out_order_no  | string | 是   | 平台订单号                     |
| out_settle_no | string | 是   | 自定义订单号                   |
| settle_desc   | int    | 是   | 分账描述                       |
| cp_extra      | string | 是   | 开发者自定义字段，回调原样回传 |

```php

    $payName='Byte';//设置驱动
    $Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
    $data = $Baidu->findOrder($order);
    // 成功 array 【自己看手册】
    // 失败 false
```

### 字节退款

| 参数名字      | 类型   | 必须 | 说明         |
| ------------- | ------ | ---- | ------------ |
| out_order_no  | string | 是   | 平台订单号   |
| out_refund_no | int    | 是   | 自定义订单号 |
| reason        | int    | 是   | 退款说明     |
| refund_amount | string | 是   | 退款金额     |

```php

    $order = [
            'out_order_no' => '',
            'out_refund_no' => time() . 'refund',
            'reason' => '就想退款，咋滴',
            'refund_amount' => 1, //退款金额，单位[分]
        ];
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
    //返回  [err_no] => 1
    //     [err_tips] => 成功
    //     [refund_no] => 1212

```

# 微信小程序

### Config

| 参数名字   | 类型   | 必须 | 说明                                                     |
| ---------- | ------ | ---- | -------------------------------------------------------- |
| appid      | int    | 是   | 小程序 appid                                             |
| secret     | int    | 是   | 小程序 secret                                            |
| mch_id     | string | 是   | 商户 mch_id                                              |
| mch_key    | string | 是   | 商户 mch_key                                             |
| notify_url | string | 是   | 异步地址                                                 |
| cert_pem   | string | 是   | cert_pem 证书                                            |
| key_pem    | string | 是   | key_pem 证书                                             |
| trade_type | string | 是   | 默认为：JSAPI。MWEB：代表微信 H5 、JSAPI：公众号或小程序 |

### token

```php

    $payName='Weixin';//驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
    //成功 array
    //失败 false
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| access_token | string | 是   | 获取到的凭证           |

### openid

```php

    $payName='Weixin';//设置驱动
    $code="";
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
    //成功 array
    //失败 false
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |
| unionid     | string | 是   | unionid     |

### 微信解密手机号

```php

    $payName='Weixin';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
    echo $phone['phoneNumber'];
    // 成功 array
    // 失败 false
```

### 微信订单查询

```php

    $payName='Weixin';//设置驱动
    $Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
    $data = $Baidu->findOrder("订单号");
    // 成功 array 【自己看手册】
    // 失败 false
```

### 微信退款

| 参数名字      | 类型    | 必须 | 说明         |
| ------------- | ------- | ---- | ------------ |
| out_trade_no  | string  | 是   | 平台订单号   |
| out_refund_no | strging | 是   | 自定义订单号 |
| refund_fee    | int     | 是   | 退款金额     |
| total_fee     | int     | 是   | 订单金额     |
| refund_desc   | string  | 是   | 退款原因     |

```php

    $order = [
             'out_trade_no' => '123',
            'total_fee' => 0.01,
            'out_refund_no' => time(),
            'refund_fee' => 0.01,
        ];
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
    //返回 成功 返回订单号  否则 false

```

# 快手小程序

### Config

| 参数名字   | 类型   | 必须 | 说明          |
| ---------- | ------ | ---- | ------------- |
| app_id     | int    | 是   | 小程序 appid  |
| app_secret | int    | 是   | 小程序 secret |
| notify_url | string | 是   | 回调地址      |
| type       | int | 是   | 类目      |

### openid

```php

    $payName='Kuaishou';//设置驱动
    $code="";
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);

```

| 返回参数    | 类型   | 必须 | 说明          |
| ----------- | ------ | ---- | ------------- |
| session_key | string | 是   | session_key   |
| open_id     | string | 是   | 用户 open_id  |
| result      | string | 是   | 状态 1 是成功 |

### 快手解密手机号

```php

    $payName='Kuaishou';//设置驱动
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
    echo $phone['phoneNumber'];
    // 成功 array
    // 失败 false
```

### 快手订单查询

```php

    $payName='Kuaishou';//设置驱动
    $Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
    $data = $Baidu->findOrder("订单号",$access_token);
    // 成功 array 【自己看手册】
```

### 快手退款

| 参数名字      | 类型    | 必须 | 说明         |
| ------------- | ------- | ---- | ------------ |
| out_trade_no  | string  | 是   | 平台订单号   |
| out_refund_no | strging | 是   | 自定义订单号 |
| refund_amount | int     | 是   | 退款金额     |
| reason        | string  | 是   | 退款原因     |
| access_token  | string  | 是   | access_token |
| attach        | string  | 否   | 自定义       |

```php

    $orders = [
            'out_order_no' => $order['out_order_no'],
            'out_refund_no' => $order['out_refund_no'],
            'reason' => $order['reason'],
            'attach' => $order['attach'],
        ];
    $data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
    //返回 成功

```

# 微信 APP

### Config

| 参数名字   | 类型   | 必须 | 说明            |
| ---------- | ------ | ---- | --------------- |
| appid      | int    | 是   | 开发平台 appid  |
| secret     | int    | 是   | 开放平台 secret |
| mch_id     | string | 是   | 商户 mch_id     |
| mch_key    | string | 是   | 商户 mch_key    |
| trade_type | string | 是   | APP             |
| notify_url | string | 是   | 异步地址        |

# 异步通知

## 字节

```php
    $pay = \Applet\Pay\Factory::getInstance('Byte')->init($config);
    $status = $pay->notifyCheck(); //验证
    if ($status) {
        $orderSn = $pay->getNotifyOrder(); //订单数据$orderSn['msg']['cp_orderno'] $orderSn['msg']['seller_uid']
        switch ($orderSn['type']) {
            case 'payment': // 支付相关回调
                /**
                 *业务处理
                */
                echo json_encode(['err_no' => 0, 'err_tips' => 'success']);exit; // 操作成功需要给头条返回的信息
                break;
            case 'refund': // 退款相关回调
                /**
                 *业务处理
                */
                echo json_encode(['err_no' => 0, 'err_tips' => 'success']);exit; // 操作成功需要给头条返回的信息
                break;
            case 'settle': // 分账相关回调
                /**
                 *业务处理
                */
                echo json_encode(['err_no' => 0, 'err_tips' => 'success']);exit; // 操作成功需要给头条返回的信息
                break;
            default: // 未知数据
                return '数据异常';
        }
    }

```

## 微信回调(通用微信 H5 支付、小程序、微信公众号) 记得改 config 配置

```php

    $pay = \Applet\Pay\Factory::getInstance('Weixin')->init($config);
    $status = $pay->notifyCheck();//验证
    if($status){
        $order = $pay->getNotifyOrder();//订单数据
        //$order['out_trade_no']//平台订单号
        //$order['transaction_id']//微信订单号
        echo 'success';exit;
    }

```

## 百度小程序回调

```php

    $pay = \Applet\Pay\Factory::getInstance('Baidu')->init($config);
    $status = $pay->notifyCheck();//验证
    if($status){
        $order = $pay->getNotifyOrder();
        //$order['tpOrderId']
        //$order['orderId']
        //$order['userId']
        echo 'success';exit;
    }

```

## 快手小程序

```php

    $pay = \Applet\Pay\Factory::getInstance('Kuaishou')->init($config);

        $status = $pay->notifyCheck(); //验证
        if ($status) {
             $order = $pay->getNotifyOrder(); //订单数据
            //$order['data']['out_order_no']//平台订单号
            echo json_encode(['result' => 1, 'message_id' => $order['message_id']]);exit;
        }


```
# 随便打赏 or start or fork
<img src="https://github.com/code-lives/Pays/blob/main/pay.jpeg" width="30%">
