<?php

namespace Applet\Pay;

interface PayInterface
{
    //设置配置
    public static function init($config);
    //获取token
    public function getToken();
    //解密手机号
    public function decryptPhone($session_key, $iv, $encryptedData);
    //获取支付回调参数
    public function getNotifyOrder();
    //前段要的支付参数
    public function getParam();
    //申请退款
    public function applyOrderRefund($order);
}
