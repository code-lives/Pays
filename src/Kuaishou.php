<?php

namespace Applet\Pay;

class Kuaishou implements PayInterface
{

    private $orderParam;
    private $app_id;
    private $app_secret;
    private $notify_url;
    private $type;
    private $token;
    private $settle_url;
    private $codedUrl = 'https://open.kuaishou.com/oauth2/mp/code2session';
    private $tokenUrl = 'https://open.kuaishou.com/oauth2/access_token';
    protected $payUrl = 'https://open.kuaishou.com/openapi/mp/developer/epay/create_order';
    protected $query = 'https://open.kuaishou.com/openapi/mp/developer/epay/query_order';
    protected $refundUrl = 'https://open.kuaishou.com/openapi/mp/developer/epay/apply_refund';
    protected $settle = 'https://open.kuaishou.com/openapi/mp/developer/epay/settle';
    protected $sendMsgUrl = 'https://open.kuaishou.com/openapi/mp/developer/message/template/send';
    protected $notifyOrder;

    public static function init($config)
    {
        if (empty($config['app_id'])) {
            throw new \Exception('not empty app_id');
        }
        if (empty($config['app_secret'])) {
            throw new \Exception('not empty secret');
        }
        if (empty($config['notify_url'])) {
            throw new \Exception('not empty notify_url');
        }
        $class = new self();
        $class->app_id = $config['app_id'];
        $class->app_secret = $config['app_secret'];
        $class->notify_url = $config['notify_url'];
        $class->settle_url = isset($config['settle_url']) ? $config['settle_url'] : $config['notify_url'];
        $class->type = isset($config['type']) ? $config['type'] : "";
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
        $bodyData = file_get_contents("php://input");
        $this->notifyOrder = json_decode($bodyData, true);
        return $this->notifyOrder;
    }
    /**
     * 设置订单号 金额 描述
     * @param string $order_no 平台订单号
     * @param int $money 订单金额
     * @param string $title 描述
     * @param string $desc 详细
     * @param string $openid 用户openid
     * @param string @access_token 获取access_token
     */
    public function set($order_no, $money, $title, $desc, $openid, $access_token)
    {
        $order = [
            'app_id' => $this->app_id,
            'out_order_no' => $order_no,
            'open_id' => $openid,
            'total_amount' => $money,
            'subject' => $title,
            'detail' => $desc,
            'type' => $this->type,
            'expire_time' => 3000,
            'attach' => '11',
            'notify_url' => $this->notify_url,
        ];
        $order['sign'] = $this->sign($order);
        $url = $this->payUrl . "?app_id=" . $this->app_id . "&access_token=" . $access_token;
        $this->orderParam = $this->curl_post_json($url, json_encode($order));
        return $this;
    }
    /**
     * @param array $map
     * @return string
     */
    public function sign(array $map)
    {
        foreach ($map as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //签名步骤二：在string后加入KEY
        $String = $String . $this->app_secret;
        //签名步骤三：MD5加密
        $String = md5($String);
        // //签名步骤四：所有字符转为大写
        return $String;
    }
    /**
     *    作用：格式化参数，签名过程需要使用
     */
    public function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    /**
     * 获取token
     * @return array
     */
    public function getToken()
    {
        $arr = [
            'grant_type' => 'client_credentials',
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
        ];
        return json_decode($this->curl_post($this->tokenUrl, $arr), true);
    }
    /**
     * 获取openid
     * @param string $code
     * @return array 成功返回数组 失败为空
     */
    public function getOpenid($code)
    {
        $data = [
            'js_code' => $code,
            'app_id' => $this->app_id,
            'app_secret' => $this->app_secret,
        ];
        $result = json_decode($this->curl_post($this->codedUrl, $data), true);
        if ($result['result'] == 1) {
            return $result['openid'] = $result['open_id'];
        }
        return $result;
    }
    /**
     * 异步回调
     * @param string $order 回调数据
     * @param string $kwaisign sign
     * @return bool true   验签通过|false 验签不通过
     */
    public function notifyCheck()
    {
        if (md5(json_encode($this->getNotifyOrder()) . $this->app_secret) != $_SERVER['HTTP_KWAISIGN']) {
            return false;
        }
        return true;
    }
    /**
     * 申请退款
     * @param string $order['out_order_no'] 订单号
     * @param string $order['out_refund_no'] 自定义退款单号
     * @param string $order['reason'] 推荐原因
     * @param string $order['attach'] 自定义字段
     * @param string $order['refund_amount'] 金额
     * @param string $order['access_token'] access_token
     */
    public function applyOrderRefund($order)
    {
        $orders = [
            'app_id' => $this->app_id,
            'out_order_no' => $order['out_order_no'],
            'out_refund_no' => $order['out_refund_no'],
            'reason' => $order['reason'],
            'notify_url' => $this->notify_url,
            'attach' => $order['attach'],
            'refund_amount' => $order['refund_amount'],
        ];
        $orders['sign'] = $this->sign($orders);
        $url = $this->refundUrl . "?app_id=" . $this->app_id . "&access_token=" . $order['access_token'];
        return json_decode($this->curl_post_json($url, json_encode($orders)), true);
    }
    /**
     * 查询订单
     * @param string $out_order_no
     * @param string $access_token
     * @param string $bodyParam
     * @param string $url
     * @param void
     */
    public function findOrder($out_order_no, $access_token)
    {
        $order = [
            'app_id' => $this->app_id,
            'out_order_no' => $out_order_no,
        ];
        $bodyParam['sign'] = $this->sign($order);
        $bodyParam['out_order_no'] = $out_order_no;
        $url = $this->query . "?app_id=" . $this->app_id . "&access_token=" . $access_token;
        return json_decode($this->curl_post_json($url, json_encode($bodyParam)), true);
    }
    /**
     * 分账
     *
     * @param  [type] $order
     * @return void
     * @author LiJie
     */
    public function settle($settle_order, $access_token)
    {
        $settle_order['app_id'] = $this->app_id;
        $settle_order['notify_url'] = $this->settle_url;
        $settle_order = $settle_order;
        $settle_order['sign'] = $this->sign($settle_order);
        $url = $this->settle . "?app_id=" . $this->app_id . "&access_token=" . $access_token;
        return json_decode($this->curl_post_json($url, json_encode($settle_order)), true);
    }
    /**
     * 发送模版消息
     *
     * @param  [type] $data
     * @param  [type] $token
     * @return void
     */
    public function sendMsg($data, $token)
    {
        return json_decode($this->curl_post($this->sendMsgUrl . $token, http_build_query($data)), true);
    }
    /**
     * curl post json传递
     *
     * @param string $url
     * @param string $data json数据
     * @return string $response
     */
    protected static function curl_post_json($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($data),
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }
    /**
     * curl get function
     * @param string $url
     * @return void
     */
    protected static function curl_get($url)
    {
        $headerArr = array("Content-type:application/x-www-form-urlencoded");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headerArr);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * 获取token 和 openid
     *
     * @param string $url
     * @param array $data
     * @return string $output
     */
    protected static function curl_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
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
        return json_decode($result, true);
    }
}
