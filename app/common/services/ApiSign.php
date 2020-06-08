<?php
/**
 * Created by PhpStorm.
 * User: dingran
 * Date: 2020/3/2
 * Time: 下午2:19
 */

namespace app\common\services;


class ApiSign
{
    /** 请求网关 */
    private $gateway = '';

    /** 密钥 */
    private $key;

    /** 请求的参数 */
    private $parameters;

    private $sign_type = 'MD5';

    public function __construct()
    {
    }

    public function setGateWay($gateWay)
    {
        $this->gateway = $gateWay;
        return $this;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function setSignType($signType)
    {
        $this->sign_type = $signType;
        return $this;
    }

    /**
     *获取参数值
     */
    function getParameter($parameter) {
        return isset($this->parameters[$parameter]) ? $this->parameters[$parameter] : '';
    }

    /**
     *设置参数值
     */
    function setParameter($parameter, $parameterValue) {
        $this->parameters[$parameter] = $parameterValue;
    }

    /**
     * 一次性设置参数
     */
    function setReqParams($post, $filterField = null){
        if($filterField !== null){
            forEach($filterField as $k=>$v){
                unset($post[$v]);
            }
        }

        //判断是否存在空值，空值不提交
        forEach($post as $k=>$v){
            if(empty($v)){
                unset($post[$k]);
            }
        }

        $this->parameters = $post;
    }

    /**
     *获取所有请求的参数
     *@return array
     */
    function getAllParameters() {
        return $this->parameters;
    }

    /**
     * 取得链接
     */
    public function getLink()
    {
        $para = $this->buildRequestPara();

        return $this->gateway . '?' . $this->createLinkstringUrlencode($para);
    }

    /**
     * 验证消息是否是合法消息
     */
    public function verify()
    {
        // 判断请求是否为空
        if (empty($_POST) && empty($_GET)) {
            return false;
        }

        $data = $_POST ?: $_GET;

        $this->setReqParams($data);

        // 生成签名结果
        $is_sign = $this->getSignVeryfy($data['sign']);

        if ($is_sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成要请求的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    private function buildRequestPara()
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter();

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;

        return $para_sort;
    }

    /**
     * 生成签名
     * @param $para_temp 请求前的参数数组
     * @return 签名
     */
    public function generateSign()
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter();

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        return $mysign;
    }

    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    private function paraFilter()
    {
        $para_filter = array();

        foreach($this->parameters as $k => $v) {
            if("" != $v && "sign" != $k) {
                $para_filter[$k] = $v;
            }
        }

        return $para_filter;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    private function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }

    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    private function buildRequestMysign($para_sort)
    {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $mysign = '';
        switch (strtoupper(trim($this->sign_type))) {
            case 'MD5':
                $mysign = $this->md5Sign($prestr, $this->key);
                break;
            default:
                $mysign = '';
        }

        return $mysign;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstring($para)
    {
        $arg = '';

        foreach ($para as $key => $val) {
            $arg .= $key . '=' . urlencode($val) . '&';
        }

        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    private function createLinkstringUrlencode($para)
    {
        $arg = '';

        foreach ($para as $key => $val) {
            $arg .= $key . '=' . urlencode($val) . '&';
        }

        //去掉最后一个&字符
        $arg = substr($arg, 0, count($arg) - 2);

        //如果存在转义字符，那么去掉转义
        if (get_magic_quotes_gpc()) {
            $arg = stripslashes($arg);
        }

        return $arg;
    }

    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Sign($prestr, $key)
    {
        $prestr = $prestr . $key;
        return md5($prestr);
    }

    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    private function md5Verify($prestr, $sign, $key)
    {
        $prestr = $prestr . $key;
        $mysgin = md5($prestr);

        if ($mysgin == $sign) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取返回时的签名验证结果
     * @param $para_temp 通知返回来的参数数组
     * @param $sign 返回的签名结果
     * @return 签名验证结果
     */
    public function getSignVeryfy($sign)
    {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = $this->paraFilter();

        //对待签名参数数组排序
        $para_sort = $this->argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = $this->createLinkstring($para_sort);

        $is_sgin = false;
        switch (strtoupper(trim($this->sign_type))) {
            case 'MD5':
                $is_sgin = $this->md5Verify($prestr, $sign, $this->key);
                break;
            default:
                $is_sgin = false;
        }

        return $is_sgin;
    }
}