<?php

namespace Applet\Pay;

class Baidu
{
    private $appKey;
    private $payappKey;
    private $dealId;
    private $rsaPriKeyStr;
    private $signFieldsRange = 1;
    private $rsaPubKeyStr;
    private $appid;
    private $applyOrderRefundUrl = 'https://openapi.baidu.com/rest/2.0/smartapp/pay/paymentservice/applyOrderRefund';
    protected $findByTpOrderIdUrl = "https://openapi.baidu.com/rest/2.0/smartapp/pay/paymentservice/findByTpOrderId";
    private $appSecret;
    private $isSkipAudit;
    private $orderParam;

    public  function init($config)
    {
        if (!isset($config['appid']) || empty($config['appid'])) throw new \Exception('not empty appid');
        if (!isset($config['appkey']) || empty($config['appkey'])) throw new \Exception('not empty appkey');
        if (!isset($config['payappKey']) || empty($config['payappKey'])) throw new \Exception('not empty payappKey');
        if (!isset($config['dealId']) || empty($config['dealId'])) throw new \Exception('not empty dealId');
        if (!isset($config['rsaPriKeyStr']) || empty($config['rsaPriKeyStr'])) throw new \Exception('not empty rsaPriKeyStr');
        if (!isset($config['rsaPubKeyStr']) || empty($config['rsaPubKeyStr'])) throw new \Exception('not empty rsaPubKeyStr');
        if (!isset($config['appSecret']) || empty($config['appSecret'])) throw new \Exception('not empty appSecret');

        $class = new self();
        $class->appid = $config['appid'];
        $class->appKey = $config['appkey'];
        $class->payappKey = $config['payappKey'];
        $class->isSkipAudit = isset($config['isSkipAudit']) ? $config['isSkipAudit'] : 0;
        $class->dealId = $config['dealId'];
        $class->rsaPriKeyStr = $config['rsaPriKeyStr'];
        $class->rsaPubKeyStr = $config['rsaPubKeyStr'];
        $class->appSecret = $config['appSecret'];
        return $class;
    }
    public function getParam()
    {
        return $this->orderParam;
    }
    /**
     * 设置订单号 金额 描述
     * @param string $rder_no 平台订单号
     * @param int $money 订单金额
     * @param string $title 描述
     * 
     */
    public function set($order_no, $money, $title = '')
    {
        $this->orderParam['totalAmount'] = $money * 100;
        $this->orderParam['tpOrderId'] = $order_no;
        $this->orderParam['dealId'] = $this->dealId;
        $this->orderParam['appKey'] = $this->payappKey;
        $this->orderParam['dealTitle'] = $title;
        $sign = self::sign($this->orderParam, $this->rsaPriKeyStr);
        $this->orderParam['rsaSign'] = $sign;
        $this->orderParam['signFieldsRange'] = $this->signFieldsRange;
        return $this;
    }

    /**
     * 获取token
     */
    public function getToken()
    {
        $url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=" . $this->appKey . "&client_secret=" . $this->appSecret . "&scope=smartapp_snsapi_base";
        $result = json_decode($this->curl_get($url), true);
        return $result;
    }
    /**
     * 获取openid 
     * @param string $code 
     * @return array 成功返回数组 失败为空
     */
    public function getOpenid($code)
    {
        $url = "https://spapi.baidu.com/oauth/jscode2sessionkey?code=" . $code . "&client_id=" . $this->appKey . "&sk=" . $this->appSecret;
        $result = json_decode($this->curl_get($url), true);
        if (isset($result['openid'])) {
            return $result;
        } else {
            return false;
        }
    }
    /**
     * @desc 异步回调
     * @param array data 回调参数$_POST
     * @return bool true 验签通过|false 验签不通过
     */
    public function notifyCheck($data)
    {
        return self::checkSign($data, $this->rsaPubKeyStr);
    }
    /**
     * 申请退款
     *
     */
    public function applyOrderRefund($order)
    {
        $data = [
            'access_token' => $order['access_token'],
            'bizRefundBatchId' => time(),
            'isSkipAudit' => $this->isSkipAudit,
            'orderId' => $order['orderId'],
            'refundReason' => $order['refundReason'],
            'refundType' => $order['refundType'],
            'tpOrderId' => $order['tpOrderId'],
            'userId' => $order['userId'],
            'pmAppKey' => $this->payappKey,
        ];
        $result = json_decode($this->curl_post($this->applyOrderRefundUrl, $data), true);
        if (isset($result['errno']) && $result['errno'] == 0) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 订单查询
     * @param array order 参数组合
     * @return array 订单信息
     */
    public function findOrder($order)
    {
        if (empty($order)) {
            return false;
        }
        $string = "?access_token=" . $order['access_token'] . "&tpOrderId=" . $order['tpOrderId'] . "&pmAppKey=" . $this->payappKey;
        $result = json_decode($this->curl_get($this->findByTpOrderIdUrl . $string), true);
        if (isset($result['errno']) && $result['errno'] == 0) {
            return $result['data'];
        }
        return false;
    }
    protected static function curl_get($url)
    {
        $headerArr = array("Content-type:application/x-www-form-urlencoded");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * @desc post 用于退款
     */
    protected static function curl_post($url, $data = array())
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        // POST数据

        curl_setopt($ch, CURLOPT_POST, 1);

        // 把post的变量加上

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }
    /**
     * 解密手机号
     * 
     * @param string $session_key 前端传递的session_key
     * @param string $iv 		  前端传递的iv
     * @param string $ciphertext  前端传递的ciphertext
     */
    public function decryptPhone($session_key, $iv, $ciphertext)
    {
        $plaintext = self::decrypt($ciphertext, $iv, $this->appKey, $session_key);
        if ($plaintext) {
            return json_decode($plaintext, true);
        }
        return false;
    }
    /**
     *
     * @param string $ciphertext    待解密数据，返回的内容中的data字段
     * @param string $iv            加密向量，返回的内容中的iv字段
     * @param string $app_key       创建小程序时生成的app_key
     * @param string $session_key   登录的code换得的
     * @return string | false
     */
    private static function decrypt($ciphertext, $iv, $app_key, $session_key)
    {

        $session_key = base64_decode($session_key);
        $iv = base64_decode($iv);
        $ciphertext = base64_decode($ciphertext);

        $plaintext = false;

        $plaintext = openssl_decrypt($ciphertext, "AES-192-CBC", $session_key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);

        if ($plaintext == false) {
            return false;
        }

        // trim pkcs#7 padding
        $pad = ord(substr($plaintext, -1));
        $pad = ($pad < 1 || $pad > 32) ? 0 : $pad;
        $plaintext = substr($plaintext, 0, strlen($plaintext) - $pad);

        // trim header
        $plaintext = substr($plaintext, 16);
        // get content length
        $unpack = unpack("Nlen/", substr($plaintext, 0, 4));
        // get content
        $content = substr($plaintext, 4, $unpack['len']);
        // get app_key
        $app_key_decode = substr($plaintext, $unpack['len'] + 4);

        return $app_key == $app_key_decode ? $content : false;
    }
    /**
     * @desc 使用私钥生成签名字符串
     * @param array $assocArr 入参数组
     * @param string $rsaPriKeyStr 私钥原始字符串，不含PEM格式前后缀
     * @return string 签名结果字符串
     * @throws Exception
     */
    public static function sign(array $assocArr, $rsaPriKeyStr)
    {

        $sign = '';
        if (empty($rsaPriKeyStr) || empty($assocArr)) {
            return $sign;
        }

        if (!function_exists('openssl_pkey_get_private') || !function_exists('openssl_sign')) {
            throw new \Exception("openssl扩展不存在");
        }

        $rsaPriKeyPem = self::convertRSAKeyStr2Pem($rsaPriKeyStr, 1);

        $priKey = openssl_pkey_get_private($rsaPriKeyPem);

        if (isset($assocArr['sign'])) {
            unset($assocArr['sign']);
        }
        // 参数按字典顺序排序
        ksort($assocArr);
        $parts = array();
        foreach ($assocArr as $k => $v) {
            $parts[] = $k . '=' . $v;
        }
        $str = implode('&', $parts);
        openssl_sign($str, $sign, $priKey);
        openssl_free_key($priKey);
        return base64_encode($sign);
    }
    /**
     * @desc 使用公钥校验签名
     * @param array $assocArr 入参数据，签名属性名固定为rsaSign
     * @param string $rsaPubKeyStr 公钥原始字符串，不含PEM格式前后缀
     * @return bool true 验签通过|false 验签不通过
     * @throws Exception
     */
    public static function checkSign(array $assocArr, $rsaPubKeyStr)
    {

        if (!isset($assocArr['rsaSign']) || empty($assocArr) || empty($rsaPubKeyStr)) {

            return false;
        }
        if (!function_exists('openssl_pkey_get_public') || !function_exists('openssl_verify')) {

            throw new \Exception("openssl扩展不存在");
        }
        $sign = $assocArr['rsaSign'];

        unset($assocArr['rsaSign']);

        if (empty($assocArr)) {
            return false;
        }
        // 参数按字典顺序排序
        ksort($assocArr);

        $parts = array();

        foreach ($assocArr as $k => $v) {

            $parts[] = $k . '=' . $v;
        }
        $str = implode('&', $parts);

        $sign = base64_decode($sign);

        $rsaPubKeyPem = self::convertRSAKeyStr2Pem($rsaPubKeyStr);

        $pubKey = openssl_pkey_get_public($rsaPubKeyPem);

        $result = (bool)openssl_verify($str, $sign, $pubKey);


        return $result;
    }
    /**
     * @desc 将密钥由字符串（不换行）转为PEM格式
     * @param string $rsaKeyStr 原始密钥字符串
     * @param int $keyType 0 公钥|1 私钥，默认0
     * @return string PEM格式密钥
     * @throws Exception
     */
    public static function convertRSAKeyStr2Pem($rsaKeyStr, $keyType = 0)
    {

        $pemWidth = 64;
        $rsaKeyPem = '';
        $begin = '-----BEGIN ';
        $end = '-----END ';
        $key = ' KEY-----';
        $type = $keyType ? 'PRIVATE' : 'PUBLIC';
        $rsa = $keyType ? 'RSA ' : '';
        $keyPrefix = $begin . $rsa . $type . $key;
        $keySuffix = $end . $rsa . $type . $key;
        $rsaKeyPem .= $keyPrefix . "\n";
        $rsaKeyPem .= wordwrap($rsaKeyStr, $pemWidth, "\n", true) . "\n";
        $rsaKeyPem .= $keySuffix;

        if (!function_exists('openssl_pkey_get_public') || !function_exists('openssl_pkey_get_private')) {
            return false;
        }

        if ($keyType == 0 && false == openssl_pkey_get_public($rsaKeyPem)) {
            return false;
        }

        if ($keyType == 1 && false == openssl_pkey_get_private($rsaKeyPem)) {
            return false;
        }

        return $rsaKeyPem;
    }
}
