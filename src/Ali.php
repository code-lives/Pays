<?php

namespace Applet\Pay;

use PHPUnit\Framework\MockObject\Stub\ReturnStub;

// implements PayInterface
class Ali
{
    private $orderParam;
    private $appid;
    private $version = "1.0";
    private $sign_type = "RSA2";
    private $charset = "UTF-8";
    private $format = "JSON";
    private $payMothod = "alipay.trade.create";
    private $openidMothod = "alipay.system.oauth.token";
    private $queryMothod = "alipay.trade.query";
    private $refundMothod = "alipay.trade.refund";
    private $templateMothod = "alipay.open.app.mini.templatemessage.send";
    private $notify_url;
    private $gateway = "https://openapi.alipay.com/gateway.do";
    private $secret; //AES
    private $privateKey; //应用私钥
    private $publicKey; //支付宝公钥
    private $notifyOrder;
    public static function init($config)
    {
        if (empty($config['appid'])) {
            throw new \Exception('not empty app_id');
        }
        if (empty($config['secret'])) {
            throw new \Exception('not empty secret');
        }
        $class = new self();
        $class->appid = $config['appid'];
        $class->secret = isset($config['secret']) ? $config['secret'] : "";
        $class->notify_url = isset($config['notify_url']) ? $config['notify_url'] : "";
        $class->privateKey = isset($config['privateKey']) ? $config['privateKey'] : "";
        $class->publicKey = isset($config['publicKey']) ? $config['publicKey'] : "";
        return $class;
    }
    /**
     * 获取下单信息
     */
    public function getParam()
    {
        return $this->orderParam;
    }
    /**
     * 获取异步订单信息
     */
    public function getNotifyOrder()
    {
        $this->notifyOrder = $_POST;
        return $this->notifyOrder;
    }
    /**
     * 设置订单号 金额 描述
     * @param string $order_no 平台订单号
     * @param int $money 订单金额
     * @param string $title 描述
     * @param string $desc 订单附加信息
     * @param string $openid 用户buyer_id
     */
    public function set($order_no, $money, $title, $desc, $openid)
    {
        $order = [
            'app_id' => $this->appid,
            'method' => $this->payMothod,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => $this->version,
            'notify_url' => $this->notify_url,
            'biz_content' => json_encode([
                'out_trade_no' => $order_no,
                'total_amount' => $money / 10000,
                'subject' => $title,
                // 'body' => urlencode($desc),
                'buyer_id' => $openid,
            ], JSON_UNESCAPED_UNICODE),
        ];
        $order['sign'] = $this->sign($order);
        $this->orderParam = json_decode($this->curl_post($this->gateway, $order), true);
        return $this;
    }
    /**
     * 获取openid 也是user_id
     * @param string $code
     * @return array 成功返回数组 失败为空
     */
    public function getOpenid($code)
    {
        $order = [
            'app_id' => $this->appid,
            'method' => $this->openidMothod,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => $this->version,
            'grant_type' => 'authorization_code',
            'code' => $code,
        ];
        $order['sign'] = $this->sign($order);
        return json_decode($this->curl_post($this->gateway, $order), true);
    }
    /**
     * 解密手机号
     * @param string $encryptedData 前端传递的encryptedData
     */
    public function decryptPhone($encryptedData)
    {
        return json_decode(openssl_decrypt(base64_decode($encryptedData), 'AES-128-CBC', base64_decode($this->secret), OPENSSL_RAW_DATA), true);
    }
    /**
     * 订单查询
     * @param string $order_no [out_trade_no,trade_no]
     * @return array 订单信息
     */
    public function findOrder($order_no)
    {
        $order = [
            'app_id' => $this->appid,
            'method' => $this->queryMothod,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => $this->version,
            'notify_url' => $this->notify_url,
            'biz_content' => json_encode($order_no, JSON_UNESCAPED_UNICODE),
        ];
        $order['sign'] = $this->sign($order);
        return json_decode($this->curl_post($this->gateway, $order), true);
    }
    /**
     * 订单查询
     * @param string $order_no [out_trade_no,trade_no]
     * @return array 订单信息
     */
    public function applyOrderRefund($order_no)
    {
        $order = [
            'app_id' => $this->appid,
            'method' => $this->refundMothod,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => $this->version,
            'notify_url' => $this->notify_url,
            'biz_content' => json_encode($order_no, JSON_UNESCAPED_UNICODE),
        ];
        $order['sign'] = $this->sign($order);
        return json_decode($this->curl_post($this->gateway, $order), true);
    }
    /**
     * 发送模版消息
     * @param array $message [to_user_id,user_template_id,page,data]
     * @return void
     */
    public function sendMsg($message)
    {
        $order = [
            'app_id' => $this->appid,
            'method' => $this->templateMothod,
            'format' => $this->format,
            'charset' => $this->charset,
            'sign_type' => $this->sign_type,
            'timestamp' => date("Y-m-d H:i:s"),
            'version' => $this->version,
            'notify_url' => $this->notify_url,
            'biz_content' => json_encode($message, JSON_UNESCAPED_UNICODE),
        ];
        $order['sign'] = $this->sign($order);
        return json_decode($this->curl_post($this->gateway, $order), true);
    }
    /**
     * @param array $map
     * @return string
     */
    public function sign(array $map)
    {
        $string = $this->formatBizQueryParaMap($map);
        $secret = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($this->privateKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($secret) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        openssl_sign($string, $sign, $secret, OPENSSL_ALGO_SHA256);
        return base64_encode($sign);
    }
    /**
     * 异步回调
     * @return bool true   验签通过|false 验签不通过
     */
    public function notifyCheck()
    {
        $order = $this->getNotifyOrder();
        $sign = $order['sign'];
        unset($order['sign']);
        unset($order['sign_type']);
        $string = $this->formatBizQueryParaMap($order);
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($this->publicKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        $result = FALSE;
        $result = (openssl_verify($string, base64_decode($sign), $res, OPENSSL_ALGO_SHA256) === 1);
        if ($order['trade_status'] == "TRADE_SUCCESS" && $result) {
            return true;
        }
        return false;;
    }
    /**
     *    作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($params)
    {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, "UTF-8");
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }
    function characet($data, $targetCharset)
    {
        if (!empty($data)) {
            $fileType = "UTF-8";
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
            }
        }
        return $data;
    }
    public function curl_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        $response = mb_convert_encoding($response, 'UTF-8', 'GBK');
        curl_close($ch);
        return $response;
    }
}
