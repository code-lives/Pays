<?php

namespace Applet\Pay;

class Byte implements PayInterface
{

    private $orderParam;
    private $app_id;
    private $secret;
    private $merchant_id;
    private $salt;
    private $valid_time;
    private $notify_url;
    private $settle_url;
    private $token;
    private $codeUrl = 'https://developer.toutiao.com/api/apps/v2/jscode2session?';
    private $tokenUrl = 'https://developer.toutiao.com/api/apps/v2/token';
    protected $payUrl = 'https://developer.toutiao.com/api/apps/ecpay/v1/create_order';
    protected $query = 'https://developer.toutiao.com/api/apps/ecpay/v1/query_order';
    protected $refundUrl = 'https://developer.toutiao.com/api/apps/ecpay/v1/create_refund';
    protected $settle = 'https://developer.toutiao.com/api/apps/ecpay/v1/settle';
    protected $sendMsgUrl = 'https://developer.toutiao.com/api/apps/subscribe_notification/developer/v1/notify';
    protected $payOrder;
    private $notifyOrder;

    public static function init($config)
    {
        if (empty($config['app_id'])) {
            throw new \Exception('not empty app_id');
        }
        if (empty($config['secret'])) {
            throw new \Exception('not empty secret');
        }
        if (empty($config['merchant_id'])) {
            throw new \Exception('not empty merchant_id');
        }
        if (empty($config['salt'])) {
            throw new \Exception('not empty salt');
        }
        if (empty($config['notify_url'])) {
            throw new \Exception('not empty notify_url');
        }
        if (empty($config['token'])) {
            throw new \Exception('not empty notify_url');
        }
        $class = new self();
        $class->app_id = $config['app_id'];
        $class->secret = $config['secret'];
        $class->token = $config['token'];
        $class->merchant_id = $config['merchant_id'];
        $class->salt = $config['salt'];
        $class->settle_url = isset($config['settle_url']) ? $config['settle_url'] : $config['notify_url'];
        $class->notify_url = $config['notify_url'];
        $class->valid_time = isset($config['valid_time']) ? $config['valid_time'] : time() + 900;
        return $class;
    }
    /**
     * 获取下单信息
     */
    public function getParam()
    {
        return $this->payOrder;
    }
    /**
     * 获取异步订单信息
     */
    public function getNotifyOrder()
    {
        $data = file_get_contents("php://input");
        $order = json_decode($data, true);
        $order['msg'] = json_decode($order['msg'], true);
        $this->notifyOrder = $order;
        return $this->notifyOrder;
    }
    /**
     * 设置订单号 金额 描述
     * @param string $rder_no 平台订单号
     * @param int $money 订单金额
     * @param string $title 描述
     *
     */
    public function set($order_no, $money, $title, $desc = '')
    {
        $this->orderParam["out_order_no"] = $order_no;
        $this->orderParam["total_amount"] = $money;
        $this->orderParam["subject"] = $title;
        $this->orderParam["body"] = $desc;
        $this->orderParam["notify_url"] = $this->notify_url;
        $this->orderParam["valid_time"] = $this->valid_time;
        $this->orderParam["app_id"] = $this->app_id;
        $data = json_encode(["sign" => $this->sign($this->orderParam)] + $this->orderParam);
        return json_decode($this->curl_post($this->payUrl, $data), true);
    }
    /**
     * @param array $map
     * @return string
     */
    public function sign(array $map)
    {
        $rList = array();
        foreach ($map as $k => $v) {
            if ($k == "other_settle_params" || $k == "app_id" || $k == "sign" || $k == "thirdparty_id") {
                continue;
            }
            $value = trim(strval($v));
            $len = strlen($value);
            if ($len > 1 && substr($value, 0, 1) == "\"" && substr($value, $len, $len - 1) == "\"") {
                $value = substr($value, 1, $len - 1);
            }
            $value = trim($value);
            if ($value == "" || $value == "null") {
                continue;
            }
            array_push($rList, $value);
        }
        array_push($rList, $this->salt);
        sort($rList, 2);
        return md5(implode('&', $rList));
    }
    /**
     * 获取token
     */
    public function getToken()
    {
        $arr = [
            'grant_type' => 'client_credential',
            'appid' => $this->app_id,
            'secret' => $this->secret,
        ];

        return json_decode($this->curl_post($this->tokenUrl, json_encode($arr)), true);
    }
    /**
     * 获取 openid
     *
     * @param  string $code
     * @param  string $anonymous_code
     * @return void
     * @author LiJie
     */
    public function getOpenid($code, $anonymous_code = "")
    {
        $url = $this->codeUrl . "appid=" . $this->app_id . "&secret=" . $this->secret . "&code=" . $code;
        if ($anonymous_code) {
            $url .= "&anonymous_code=" . $anonymous_code;
        }
        return json_decode($this->curl_get($url), true);
    }
    /**
     * 异步回调
     * @param  $order 回调数据
     * @return bool true   验签通过|false 验签不通过
     */
    public function notifyCheck()
    {
        $order = $this->getNotifyOrder();
        $data = [
            $order['timestamp'],
            $order['nonce'],
            json_encode($order['msg']),
            $this->token,
        ];
        sort($data, SORT_STRING);
        $str = implode('', $data);
        if (!strcmp(sha1($str), $order['msg_signature'])) {
            return true;
        }
        return false;
    }
    /**
     * 申请退款
     *
     */
    public function applyOrderRefund($order)
    {
        $order['notify_url'] = $this->notify_url;
        $order['app_id'] = $this->app_id;
        return json_decode($this->curl_post($this->refundUrl, json_encode(['sign' => $this->sign($order)] + $order)), true);
    }
    /**
     * 订单查询
     * @param string $out_order_no 订单号
     * @return array 订单信息
     */
    public function findOrder($out_order_no)
    {
        if (empty($out_order_no)) {
            return false;
        }
        $order = [
            'out_order_no' => $out_order_no,
            'app_id' => $this->app_id,
        ];
        $order['sign'] = $this->sign($order);
        return json_decode($this->curl_post($this->query, json_encode($order)), true);
    }
    /**
     * 分账
     *
     * @param  [type] $order
     * @return void
     * @author LiJie
     */
    public function settle($order)
    {
        $data = [
            'app_id' => $this->app_id,
            'out_settle_no' => $order['out_settle_no'],
            'out_order_no' => $order['out_order_no'],
            'settle_desc' => $order['settle_desc'],
            'notify_url' => $this->settle_url,
            'cp_extra' => $order['cp_extra'],
        ];
        $data['sign'] = $this->sign($data);
        $result = json_decode($this->curl_post($this->settle, json_encode($data)), true);
        return $result;
    }
    /**
     * 发送模版消息
     *
     * @param  [type] $data
     * @param  [type] $token
     */
    public function sendMsg($data, $token)
    {
        $data['access_token'] = $token;
        $data['app_id'] = $this->app_id;
        return json_decode($this->curl_post($this->sendMsgUrl, json_encode($data)), true);
    }
    /**
     * 解密手机号
     *
     * @param string $session_key 前端传递的session_key
     * @param string $iv           前端传递的iv
     * @param string $encryptedData  前端传递的encryptedData
     */
    public function decryptPhone($session_key, $iv, $encryptedData)
    {
        if (strlen($session_key) != 24) {
            return false;
        }
        $aesKey = base64_decode($session_key);
        if (strlen($iv) != 24) {
            return false;
        }
        $aesIV = base64_decode($iv);
        $aesCipher = base64_decode($encryptedData);
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $dataObj = json_decode($result);
        if ($dataObj == null) {
            return false;
        }
        if ($dataObj->watermark->appid != $this->app_id) {
            return false;
        }
        return json_decode($result, true);
    }
    protected static function curl_get($url)
    {
        $headerArr = array("Content-type:application/x-www-form-urlencoded");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $output = curl_exec($ch);
        if (!$output) {
            throw new \Exception(curl_errno($ch));
        }
        curl_close($ch);
        return $output;
    }
    /**
     * @desc post 用于退款
     */
    protected static function curl_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json;charset=utf-8',
                'Content-Length: ' . strlen($data),
            )
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        if (!$output) {
            throw new \Exception(curl_errno($ch));
        }
        curl_close($ch);
        return $output;
    }
}
