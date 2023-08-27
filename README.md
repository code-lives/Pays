<p align="center">
<a href="https://packagist.org/packages/code-lives/applet-pays" target="_blank"><img src="https://img.shields.io/packagist/v/code-lives/applet-pays?include_prereleases" alt="GitHub forks"></a>
<a href="https://packagist.org/packages/code-lives/applet-pays" target="_blank"><img src="https://img.shields.io/github/stars/code-lives/Pays?style=social" alt="GitHub forks"></a>
<a href="https://github.com/code-lives/Pays/fork" target="_blank"><img src="https://img.shields.io/github/forks/code-lives/Pays?style=social" alt="GitHub forks"></a>

</p>

#### 公告 2023-8-27

> 有想维护这个 SDK 的小伙伴吗？我可能不从事这个行业了， SDK 的配置我就没有了 😭 ，只要有下面配置的小伙伴都可以参与，乐意维护的 可以加下面的飞书群或者加我微信 l948870149

> 大家也可以提交相应的 pr，下面我把微信、百度、抖音、快手、支付宝对应的仓库位置已补充 👇

|                                               第三方                                                | token | openid | 支付 | 回调 | 退款 | 订单查询 | 解密手机号 | 分账 | 模版消息 | 仓库                                           |
| :-------------------------------------------------------------------------------------------------: | :---: | :----: | :--: | :--: | :--: | :------: | :--------: | :--: | :------: | ---------------------------------------------- |
|          [微信小程序](https://pay.weixin.qq.com/wiki/doc/api/wxa/wxa_api.php?chapter=9_1)           |   ✓   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     ✓      |  x   |    ✓     | [地址](https://github.com/code-lives/weixin)   |
|           [百度小程序](https://smartprogram.baidu.com/docs/develop/function/tune_up_2.0/)           |   ✓   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     ✓      |  x   |    ✓     | [地址](https://github.com/code-lives/baidu)    |
| [抖音小程序](https://developer.open-douyin.com/docs/resource/zh-CN/mini-app/introduction/overview/) |   ✓   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     ✓      |  ✓   |    ✓     | [地址](https://github.com/code-lives/byte)     |
|       [快手小程序](https://mp.kuaishou.com/docs/develop/server/epay/interfaceDefinition.html)       |   ✓   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     ✓      |  ✓   |    x     | [地址](https://github.com/code-lives/kuaishou) |
|                       [支付宝小程序](https://opendocs.alipay.com/mini/03l5wn)                       |   x   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     ✓      |  x   |    ✓     | [地址](https://github.com/code-lives/ali)      |
|                                               微信 h5                                               |   x   |   x    |  ✓   |  ✓   |  ✓   |    ✓     |     x      |  x   |    x     | [地址](https://github.com/code-lives/weixin)   |
|                                              微信 APP                                               |   x   |   ✓    |  ✓   |  ✓   |  ✓   |    ✓     |     x      |  x   |    x     | [地址](https://github.com/code-lives/weixin)   |
|                                             微信公众号                                              |   x   |   x    |  ✓   |  ✓   |  ✓   |    ✓     |     x      |  x   |    ✓     | [地址](https://github.com/code-lives/weixin)   |

## 安装

```php
composer require code-lives/applet-pays 5.9
```

### ⚠️ 注意

> 金额单位分 100=1 元

> 微信支付未使用 APIv3 接口规则

> 获取 openid 接口 统一 openid 字段（因快手返回的 open_id）

> 返回结果 array 由开发者自行判断

> 抖音小程序由字节小程序转变而来，支持多端（头条、抖音、今日头条等关联应用）

# 预下单

```php
// 第一种使用方法 Factory:: ide 自动提示 Weixin
$pay= \Applet\Pay\Factory::Weixin()->init($config)->set("订单号","金额","描述")->getParam();

// 第二种方法
$PayName='Baidu';//百度
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getParam();

$PayName='Byte';//抖音
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","描述")->getParam();

$PayName='Weixin';//微信
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","openid")->getParam();

$PayName='Kuaishou';//快手
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述",'openid', 'access_token')->getParam();

$PayName='Ali';//支付宝小程序
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述",'openid')->getParam();

$PayName='Weixin';//微信公众号【appid 和secret 换成公众号的】
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述","openid")->getParam();

$PayName='Weixin';//微信H5【appid 和secret 换成公众号的】
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getH5Param();

$PayName='Weixin';//微信APP (没有openid)
$pay= \Applet\Pay\Factory::getInstance($PayName)->init($config)->set("订单号","金额","描述")->getParam();

```

# 百度小程序

### Config

| 参数名字        | 类型   | 必须 | 说明                                                            |
| --------------- | ------ | ---- | --------------------------------------------------------------- |
| appkey          | string | 是   | 百度小程序 appkey                                               |
| payappKey       | string | 是   | 百度小程序支付 appkey                                           |
| appSecret       | string | 是   | 百度小程序 aapSecret                                            |
| dealId          | int    | 是   | 百度小程序支付凭证                                              |
| isSkipAudit     | int    | 是   | 默认为 0； 0：不跳过开发者业务方审核；1：跳过开发者业务方审核。 |
| rsaPriKeyStr    | string | 是   | 私钥（只需要一行长串，不需要文件）                              |
| rsaPubKeyStr    | string | 是   | 百度小程序支付的平台公钥(支付回调需要)                          |
| notifyUrl       | string | 否   | 异步回调地址                                                    |
| refundNotifyUrl | string | 否   | 退款异步回调地址                                                |

### Token

```php
$PayName='Baidu';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| session_key  | string | 是   | session_key            |
| access_token | string | 是   | 获取到的凭证           |

### Openid

```php
$PayName='Baidu';//设置驱动
$code="";
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |

### 解密手机号

```php
$PayName='Baidu';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $ciphertext);
echo $phone['mobile'];
```

### 百度订单查询

| 参数名字     | 类型   | 必须 | 说明                 |
| ------------ | ------ | ---- | -------------------- |
| access_token | string | 是   | 根据上面的获取 token |
| tpOrderId    | string | 是   | 平台订单号           |

```php
$PayName='Baidu';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance('Baidu')->init($config);
$order = [
        'tpOrderId' => '',//订单号
        'access_token' => $Baidu->getToken()['access_token'],
    ];
$data = $Baidu->findOrder($order);
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
```

### 百度小程序模版消息

```php
$data = [
    "touser_openId" => "",
    "template_id" => "",
    "page" => "pages/index/index",
    "subscribe_id" => '百度from组件subscribe-id 一致',
    "data" => json_encode([
        'keyword1' => ['value' => "第一个参数"],
        'keyword2' => ['value' => "第二个参数"],
        'keyword3' =>  ['value' => "第三个参数"],
    ])
];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->sendMsg($data,$token);
$data=[
   "errno" => 0,
    "msg" => "success",
    "data" => array=> [
    "msg_key" => 1663314134696897
  ]
]
```

# 抖音小程序

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

### Token

```php
$PayName='Byte';//驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| access_token | string | 是   | 获取到的凭证           |

### Openid

```php
$PayName='Byte';//设置驱动
$code="";
$anonymous_code="";//可以不传
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code,$anonymous_code);
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |
| unionid     | string | 是   | unionid     |

### 解密手机号

```php
$PayName='Baidu';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
echo $phone['phoneNumber'];
```

### 订单查询

```php
$PayName='Byte';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
$data = $Baidu->findOrder("订单号");
```

### 分账

| 参数名字      | 类型   | 必须 | 说明                           |
| ------------- | ------ | ---- | ------------------------------ |
| out_order_no  | string | 是   | 平台订单号                     |
| out_settle_no | string | 是   | 自定义订单号                   |
| settle_desc   | int    | 是   | 分账描述                       |
| cp_extra      | string | 是   | 开发者自定义字段，回调原样回传 |

```php
$PayName='Byte';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
$data = $Baidu->settle($order);
```

### 退款

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
        'refund_amount' => 1, //退款金额
    ];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
//返回  [err_no] => 1
//     [err_tips] => 成功
//     [refund_no] => 1212
```

### 模版消息

```php
$data = [
        'tpl_id' =>  "模版id",
        "open_id" => $parm['openid'],
        'data' => [
            '律师' => "张三",
            "回复时间" => date('Y-m-d H:i:s', time()),
            "回复内容" => "我回复你啦",
        ],
        "page" => "pages/index/index",
    ];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->sendMsg($data,$token);

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

### Token

```php
$PayName='Weixin';//驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
```

| 返回参数     | 类型   | 必须 | 说明                   |
| ------------ | ------ | ---- | ---------------------- |
| expires_in   | string | 是   | 凭证有效时间，单位：秒 |
| access_token | string | 是   | 获取到的凭证           |

### Openid

```php
$PayName='Weixin';//设置驱动
$code="";
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
```

| 返回参数    | 类型   | 必须 | 说明        |
| ----------- | ------ | ---- | ----------- |
| session_key | string | 是   | session_key |
| openid      | string | 是   | 用户 openid |
| unionid     | string | 是   | unionid     |

### 微信解密手机号

```php
$PayName='Weixin';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
echo $phone['phoneNumber'];
```

### 微信订单查询

```php
$PayName='Weixin';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
$data = $Baidu->findOrder("订单号");
```

### 微信退款

| 参数名字      | 类型   | 必须 | 说明         |
| ------------- | ------ | ---- | ------------ |
| out_trade_no  | string | 是   | 平台订单号   |
| out_refund_no | string | 是   | 自定义订单号 |
| refund_fee    | int    | 是   | 退款金额     |
| total_fee     | int    | 是   | 订单金额     |
| refund_desc   | string | 是   | 退款原因     |

```php
$order = [
        'out_trade_no' => '123',
        'total_fee' => 0.01,
        'out_refund_no' => time(),
        'refund_fee' => 0.01,
    ];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
```

### 微信小程序模版消息

```php
$data = [
    "touser" => "",
    "template_id" => "",
    "page" => "pages/index/index",
    "miniprogram_state" => "developer",
    "lang" => "zh_CN",
    "data" => [
        'thing6' => ['value' => "第一个参数{{thing6.DATA}}"],
        'thing7' => ['value' => "第二个参数{{thing7.DATA}}"],
        'time8' =>  ['value' => "第三个参数{{time8.DATA}}"],
],
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->sendMsg($data,$token);
$data=[
    "errcode" => 0
    "errmsg" => "ok"
    "msgid" => 123456
]
```

# 支付宝小程序

> 使用密钥进行签名解密，没有使用证书签名解密。

> 订单查询、退款、参数设置可以设置其他，具体看文档。

> 返回值 看官方文档，每个返回值都不一样，自行判断，如 openid 返回[alipay_system_oauth_token_response] 退款返回[alipay_trade_create_response]

### Config

| 参数名字   | 类型   | 必须 | 说明                         |
| ---------- | ------ | ---- | ---------------------------- |
| appid      | string | 是   | 小程序 appid                 |
| secret     | string | 是   | 小程序 AES 用于手机号解密    |
| privateKey | string | 是   | 应用私钥（开发工具生成）     |
| publicKey  | string | 是   | 支付宝公钥（支付宝后台下载） |
| notify_url | string | 是   | 异步回调地址                 |

### Openid

> getOpenid 获取支付宝的用户 user_id 类似于微信的 openid

```php
$PayName='Ali';//设置驱动
$code="";
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
//返回参数
$data = array(
    [alipay_system_oauth_token_response] => Array
        (
            [access_token] => 123
            [alipay_user_id] => 123
            [auth_start] => 2023-03-26 20:56:36
            [expires_in] => 1296000
            [re_expires_in] => 2592000
            [refresh_token] => auth
            [user_id] => 123
        )
    [sign] =>
    )
```

### 支付宝小程序解密手机号

```php
$PayName='Ali';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
echo $phone['mobile'];
```

### 支付宝小程序订单查询

```php
$PayName='Ali';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
$data = $Baidu->findOrder(['out_trade_no' => '1679838318']);
```

### 支付宝小程序退款

| 参数名字      | 类型   | 必须 | 说明       |
| ------------- | ------ | ---- | ---------- |
| out_trade_no  | string | 是   | 平台订单号 |
| refund_amount | int    | 是   | 退款金额   |

```php
$orders = [
        'out_order_no' => $order['out_order_no'],
        'refund_amount' => $order['refund_amount'],
    ];
$PayName='Ali';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->applyOrderRefund($order);
```

### 支付宝小程序模版消息

模版消息设置比较麻烦。需要先到开发平台添加进入小程序进行产品绑定，在去商家平台设置[文档](https://opendocs.alipay.com/b/03ksho)

```php
$data = [
        'to_user_id' => '用户uid',
        'user_template_id' => '模版id',
        'page' => 'pages/index/index',
        'data' => json_encode([
            'keyword1' => ['value' => '1'],
            'keyword2' =>  ['value' => '2'],
            'keyword3' => ['value' => '3'],
        ]),
    ];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->sendMsg($data,$token);
```

# 快手小程序

### Token

```php
$PayName='Kuaishou';//设置驱动
$code="";
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getToken();
```

### Config

| 参数名字   | 类型   | 必须 | 说明                                |
| ---------- | ------ | ---- | ----------------------------------- |
| app_id     | int    | 是   | 小程序 appid                        |
| app_secret | int    | 是   | 小程序 secret                       |
| notify_url | string | 是   | 回调地址                            |
| settle_url | string | 是   | 结算回调地址，没有就默认 notify_url |
| type       | int    | 是   | 类目                                |

### Openid

```php
$PayName='Kuaishou';//设置驱动
$code="";
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->getOpenid($code);
```

| 返回参数    | 类型   | 必须 | 说明          |
| ----------- | ------ | ---- | ------------- |
| session_key | string | 是   | session_key   |
| openid      | string | 是   | 用户 open_id  |
| result      | string | 是   | 状态 1 是成功 |

### 快手解密手机号

```php
$PayName='Kuaishou';//设置驱动
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->decryptPhone($session_key, $iv, $encryptedData);
echo $phone['phoneNumber'];
```

### 快手订单查询

```php
$PayName='Kuaishou';//设置驱动
$Baidu = \Applet\Pay\Factory::getInstance($payName)->init($config);
$data = $Baidu->findOrder("订单号",$access_token);
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
```

### 快手结算

| 参数名字      | 类型   | 必须 | 说明         |
| ------------- | ------ | ---- | ------------ |
| out_order_no  | string | 是   | 平台订单号   |
| out_settle_no | string | 是   | 自定义订单号 |
| reason        | string | 是   | 退款原因     |
| access_token  | string | 是   | access_token |
| attach        | string | 否   | 自定义       |

```php
//注意 需要设置回调 notify_url  在config 设置 settle_url 如果没有 默认为 notify_url
$orders = [
        'out_order_no' => $order['out_order_no'],
        'out_settle_no' => $order['out_settle_no'],
        'reason' => $order['reason'],
        'attach' => $order['attach'],
    ];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->settle($order,$access_token);
```

### 订单信息同步

| 参数名字             | 类型   | 必须 | 说明                     |
| -------------------- | ------ | ---- | ------------------------ |
| out_biz_order_no     | string | 是   | 展示在用户端的唯一订单号 |
| out_order_no         | string | 是   | 小程序预下单支付订单号   |
| open_id              | string | 是   | 订单对应的用户 open id   |
| order_create_time    | string | 是   | 订单创建时间             |
| order_status         | string | 否   | 订单状态                 |
| order_path           | string | 是   | 小程序路径               |
| product_cover_img_id | string | 是   | 图片 id                  |

```php

$order = [
        'out_biz_order_no' => '',
        'out_order_no' => '',
        'open_id' => '',
        'order_create_time' => time(),
        'order_status' => 6,
        'order_path' => '',
        'product_cover_img_id' =>'',
    ];
$data = \Applet\Pay\Factory::getInstance($PayName)->init($config)->synchronousOrder($order, $token);
```

### 图片上传(订单信息同步)

```php
$data = \Applet\Pay\Factory::getInstance($PayName)->init($config)->imgUpload('图片路径', $token);
```

### 快手小程序模版消息

```php
$data = [
    "open_id" => "",
    "tpl_id" => "",
    "page" => "pages/index/index",
    "data" => [
        'key1' =>  "第一个",
        'key2' =>  "第二个",
        'key3' =>  "第三个",
    ]
];
$data= \Applet\Pay\Factory::getInstance($PayName)->init($config)->sendMsg($data,$token);
$data=[
    "err_no" => 1001,
    "err_tips" => "该用户未订阅"
]
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

## 抖音

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

## 支付宝小程序

```php
$pay = \Applet\Pay\Factory::getInstance('Ali')->init($config);
$status = $pay->notifyCheck(); //验证
if ($status) {
    $order = $pay->getNotifyOrder(); //订单数据
    //$order['out_trade_no']//平台订单号
}
```

## 飞书群

> 讨论及版本通知群、issues 通知群

![image](https://github.com/code-lives/Pays/assets/79346591/e1dc3b7e-1efb-4a6a-bc1a-3e8ea9c581a4)
