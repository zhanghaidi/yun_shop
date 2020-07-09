<?php
/**
 * Created by PhpStorm.
 * User: CHUWU
 * Date: 2019/3/7
 * Time: 22:44
 */

namespace Yunshop\Wechat\common\helper;


/**
 * Class Helper
 * @package Yunshop\Wechat\common\helper
 * 助手类，随机生成字符串，获取文件路径，上传等操作
 */
class Helper
{
    // 定义附件文件夹名
    const STATIC_FOLDER_NAME = 'static';
    const UPLOAD_FOLDER_NAME = 'upload';
    const IMAGE_FOLDER_NAME = 'images';
    const VOICE_FOLDER_NAME = 'voices';
    const VIDEO_FOLDER_NAME = 'videos';

    /**
     * 随机生成字符串，可用于图片等文件名
     * @param $length int 随机生成的字符串长度
     * @param bool $numeric
     * @return string
     */
    public static function random($length = 30, $numeric = FALSE)
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        if ($numeric) {
            $hash = '';
        } else {
            $hash = chr(rand(1, 26) + rand(0, 1) * 32 + 64);
            $length--;
        }
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed{mt_rand(0, $max)};
        }
        return $hash;
    }

    /**
     * 传入目录和文件扩展名，生成文件名，返回文件绝对路径
     * @param $dir string 文件绝对路径
     * @param $ext string 文件扩展名
     * @return string
     */
    public static function randomFileName($dir, $ext) {
        do {
            $filename = random(30) . '.' . $ext;
        } while (file_exists($dir . $filename));

        return $filename;
    }

    public static function getRootName()
    {
        return  base_path().DIRECTORY_SEPARATOR.Helper::STATIC_FOLDER_NAME.DIRECTORY_SEPARATOR.Helper::UPLOAD_FOLDER_NAME.DIRECTORY_SEPARATOR;
    }

    public static function getUploadDirName($type)
    {
        // 如images/公众号id/年份/月份/文件名.扩展名
        return  $type.DIRECTORY_SEPARATOR.\YunShop::app()->uniacid.DIRECTORY_SEPARATOR.date('Y').DIRECTORY_SEPARATOR.date('m').DIRECTORY_SEPARATOR;
    }
    public static function getAttachmentFileName()
    {
        return static::random();
    }

    public static function error($errno, $message = '')
    {
        return array(
            'errno' => $errno,
            'message' => $message,
        );
    }

    public static function file_upload($file, $type = 'image', $name = '', $compress = false) {
        $harmtype = array('asp', 'php', 'jsp', 'js', 'css', 'php3', 'php4', 'php5', 'ashx', 'aspx', 'exe', 'cgi');
        if (empty($file)) {
            return static::error(-1, '没有上传内容');
        }
        if (!in_array($type, array('image', 'thumb', 'voice', 'video', 'audio'))) {
            return static::error(-2, '未知的上传类型');
        }
        global $_W;
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $ext = strtolower($ext);
        $setting = setting_load('upload');
        switch ($type) {
            case 'image':
            case 'thumb':
                $allowExt = array('gif', 'jpg', 'jpeg', 'bmp', 'png', 'ico');
                $limit = $setting['upload']['image']['limit'];
                break;
            case 'voice':
            case 'audio':
                $allowExt = array('mp3', 'wma', 'wav', 'amr');
                $limit = $setting['upload']['audio']['limit'];
                break;
            case 'video':
                $allowExt = array('rm', 'rmvb', 'wmv', 'avi', 'mpg', 'mpeg', 'mp4');
                $limit = $setting['upload']['audio']['limit'];
                break;
        }
        $setting = $_W['setting']['upload'][$type];
        if (!empty($setting)) {
            $allowExt = array_merge($setting['extentions'], $allowExt);
        }
        if (!in_array(strtolower($ext), $allowExt) || in_array(strtolower($ext), $harmtype)) {
            return static::error(-3, '不允许上传此类文件');
        }
        if (!empty($limit) && $limit * 1024 < filesize($file['tmp_name'])) {
            return static::error(-4, "上传的文件超过大小限制，请上传小于 {$limit}k 的文件");
        }

        $result = array();
        if (empty($name) || $name == 'auto') {
            $uniacid = intval($_W['uniacid']);
            $path = "{$type}s/{$uniacid}/" . date('Y/m/');
            mkdirs(ATTACHMENT_ROOT . '/' . $path);
            $filename = file_random_name(ATTACHMENT_ROOT . '/' . $path, $ext);

            $result['path'] = $path . $filename;
        } else {
            mkdirs(dirname(ATTACHMENT_ROOT . '/' . $name));
            if (!strexists($name, $ext)) {
                $name .= '.' . $ext;
            }
            $result['path'] = $name;
        }

        $save_path = ATTACHMENT_ROOT . '/' . $result['path'];
        if (!file_move($file['tmp_name'], $save_path)) {
            return static::error(-1, '保存上传文件失败');
        }

        if ($type == 'image' && $compress) {
            file_image_quality($save_path, $save_path, $ext);
        }

        $result['success'] = true;

        return $result;
    }

    //微擎的方法
    public static function iunserializer($value) {
        if (empty($value)) {
            return array();
        }
        if (!self::is_serialized($value)) {
            return $value;
        }
        $result = unserialize($value);
        if ($result === false) {
            $temp = preg_replace_callback('!s:(\d+):"(.*?)";!s', function ($matchs){
                return 's:'.strlen($matchs[2]).':"'.$matchs[2].'";';
            }, $value);
            return unserialize($temp);
        } else {
            return $result;
        }
    }

    public static function is_serialized($data, $strict = true) {
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        if ('N;' == $data) {
            return true;
        }
        if (strlen($data) < 4) {
            return false;
        }
        if (':' !== $data[1]) {
            return false;
        }
        if ($strict) {
            $lastc = substr($data, -1);
            if (';' !== $lastc && '}' !== $lastc) {
                return false;
            }
        } else {
            $semicolon = strpos($data, ';');
            $brace = strpos($data, '}');
            if (false === $semicolon && false === $brace)
                return false;
            if (false !== $semicolon && $semicolon < 3)
                return false;
            if (false !== $brace && $brace < 4)
                return false;
        }
        $token = $data[0];
        switch ($token) {
            case 's' :
                if ($strict) {
                    if ('"' !== substr($data, -2, 1)) {
                        return false;
                    }
                } elseif (false === strpos($data, '"')) {
                    return false;
                }
            case 'a' :
            case 'O' :
                return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b' :
            case 'i' :
            case 'd' :
                $end = $strict ? '$' : '';
                return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
        }
        return false;
    }

    public static  function menu_languages() {
        $languages = array(
            array('ch'=>'简体中文', 'en'=>'zh_CN'),
            array('ch'=>'繁体中文TW', 'en'=>'zh_TW'),
            array('ch'=>'繁体中文HK', 'en'=>'zh_HK'),
            array('ch'=>'英文', 'en'=>'en'),
            array('ch'=>'印尼', 'en'=>'id'),
            array('ch'=>'马来', 'en'=>'ms'),
            array('ch'=>'西班牙', 'en'=>'es'),
            array('ch'=>'韩国', 'en'=>'ko'),
            array('ch'=>'意大利 ', 'en'=>'it'),
            array('ch'=>'日本', 'en'=>'ja'),
            array('ch'=>'波兰', 'en'=>'pl'),
            array('ch'=>'葡萄牙', 'en'=>'pt'),
            array('ch'=>'俄国', 'en'=>'ru'),
            array('ch'=>'泰文', 'en'=>'th'),
            array('ch'=>'越南', 'en'=>'vi'),
            array('ch'=>'阿拉伯语', 'en'=>'ar'),
            array('ch'=>'北印度', 'en'=>'hi'),
            array('ch'=>'希伯来', 'en'=>'he'),
            array('ch'=>'土耳其', 'en'=>'tr'),
            array('ch'=>'德语', 'en'=>'de'),
            array('ch'=>'法语', 'en'=>'fr')
        );
        return $languages;
    }

    public static function is_base64($str){
        if(!is_string($str)){
            return false;

        }
        return $str == base64_encode(base64_decode($str));
    }


    public static function iserializer($value) {
        return serialize($value);
    }

    public static function getFileType($fileType)
    {

//  - 图片（image）: 1M，支持 bmp/png/jpeg/jpg/gif 格式
//  - 语音（voice）：2M，播放长度不超过 60s，支持 mp3/wma/wav/amr 格式
//  - 视频（video）：10MB，支持MP4格式
//  - 缩略图（thumb）：64KB，支持JPG格式
//
        switch ($fileType) {
            // 图片
            case 'image/bmp':
                $type = 'bmp';
                break;
            case 'image/png':
                $type = 'png';
                break;
            case 'image/jpeg':
                $type = 'jpg';
                break;
            case 'image/gif':
                $type = 'gif';
                break;
            // 音频
            case 'audio/mpeg':
                $type = 'mp3';
                break;
            case 'video/x-ms-asf'://不确定video/x-ms-asf就是wma文件，因为video/x-ms-asf还可以是其他类型文件。只是使用finfo_file函数获取wma文件时得到的是video/x-ms-asf
                $type = 'wma';
                break;
            case 'audio/wav':
                $type = 'wav';
                break;
            case 'application/octet-stream'://不确定是不是amr文件，使用finfo_file函数获取amr文件得到的是application/octet-stream，但同时application/octet-stream也可以是很多二进制文件
                $type = 'amr';
                break;
            // 视频
            case 'video/mp4':
                $type = 'mp4';
                break;
            default:
                $type = '';
                break;
        }
        return $type;
    }
    //将错误消息转为中文显示
    public static function getErrorMessage($code,$message)
    {
        $error = [
            -1    => '系统繁忙，此时请开发者稍候再试',
            40001   => '获取access_token时AppSecret错误，或者access_token无效。请开发者认真比对AppSecret的正确性，或查看是否正在为恰当的公众号调用接口',
            40002   => '不合法的凭证类型',
            40003   => '不合法的OpenID，请开发者确认OpenID（该用户）是否已关注公众号，或是否是其他公众号的OpenID',
            40004   => '不合法的媒体文件类型',
            40005   => '不合法的文件类型',
            40006   => '不合法的文件大小',
            40007   => '不合法的媒体文件id',
            40008   => '不合法的消息类型',
            40009   => '不合法的图片文件大小',
            40010   => '不合法的语音文件大小',
            40011   => '不合法的视频文件大小',
            40012   => '不合法的缩略图文件大小',
            40013   => '不合法的AppID，请开发者检查AppID的正确性，避免异常字符，注意大小写',
            40014   => '不合法的access_token，请开发者认真比对access_token的有效性（如是否过期），或查看是否正在为恰当的公众号调用接口',
            40015   => '不合法的菜单类型',
            40016   => '不合法的按钮个数',
            40017   => '不合法的按钮个数',
            40018   => '不合法的按钮名字长度',
            40019   => '不合法的按钮KEY长度',
            40020   => '不合法的按钮URL长度',
            40021   => '不合法的菜单版本号',
            40022   => '不合法的子菜单级数',
            40023   => '不合法的子菜单按钮个数',
            40024   => '不合法的子菜单按钮类型',
            40025   => '不合法的子菜单按钮名字长度',
            40026   => '不合法的子菜单按钮KEY长度',
            40027   => '不合法的子菜单按钮URL长度',
            40028   => '不合法的自定义菜单使用用户',
            40029   => '不合法的oauth_code',
            40030   => '不合法的refresh_token',
            40031   => '不合法的openid列表',
            40032   => '不合法的openid列表长度',
            40033   => '不合法的请求字符，不能包含\uxxxx格式的字符',
            40035   => '不合法的参数',
            40038   => '不合法的请求格式',
            40039   => '不合法的URL长度',
            40050   => '不合法的分组id',
            40051   => '分组名字不合法',
            40117   => '分组名字不合法',
            40118   => 'media_id大小不合法',
            40119   => 'button类型错误',
            40120   => 'button类型错误',
            40121   => '不合法的media_id类型',
            40132   => '微信号不合法',
            40137   => '不支持的图片格式',
            40155   => '请勿添加其他公众号的主页链接',
            41001   => '缺少access_token参数',
            41002   => '缺少appid参数',
            41003   => '缺少refresh_token参数',
            41004   => '缺少secret参数',
            41005   => '缺少多媒体文件数据',
            41006   => '缺少media_id参数',
            41007   => '缺少子菜单数据',
            41008   => '缺少oauth code',
            41009   => '缺少openid',
            42001   => 'access_token超时，请检查access_token的有效期，请参考基础支持-获取access_token中，对access_token的详细机制说明',
            42002   => 'refresh_token超时',
            42003   => 'oauth_code超时',
            42007   => '用户修改微信密码，accesstoken和refreshtoken失效，需要重新授权',
            43001   => '需要GET请求',
            43002   => '需要POST请求',
            43003   => '需要HTTPS请求',
            43004   => '需要接收者关注',
            43005   => '需要好友关系',
            43019   => '需要将接收者从黑名单中移除',
            44001   => '多媒体文件为空',
            44002   => 'POST的数据包为空',
            44003   => '图文消息内容为空',
            44004   => '文本消息内容为空',
            45001   => '多媒体文件大小超过限制',
            45002   => '消息内容超过限制',
            45003   => '标题字段超过限制',
            45004   => '描述字段超过限制',
            45005   => '链接字段超过限制',
            45006   => '图片链接字段超过限制',
            45007   => '语音播放时间超过限制',
            45008   => '图文消息超过限制',
            45009   => '接口调用超过限制',
            45010   => '创建菜单个数超过限制',
            45011   => 'API调用太频繁，请稍候再试',
            45015   => '回复时间超过限制',
            45016   => '系统分组，不允许修改',
            45017   => '分组名字过长',
            45018   => '分组数量超过上限',
            45047   => '客服接口下行条数超过上限',
            45057   => '该标签下粉丝数超过10w，不允许直接删除',
            45058   => '不能修改0/1/2这三个系统默认保留的标签',
            46001   => '不存在媒体数据',
            46002   => '不存在的菜单版本',
            46003   => '不存在的菜单数据',
            46004   => '不存在的用户',
            47001   => '解析JSON/XML内容错误',
            48001   => 'api功能未授权，请确认公众号已获得该接口，可以在公众平台官网-开发者中心页中查看接口权限',
            48002   => '粉丝拒收消息（粉丝在公众号选项中，关闭了“接收消息”）',
            48004   => 'api接口被封禁，请登录mp.weixin.qq.com查看详情',
            48005   => 'api禁止删除被自动回复和自定义菜单引用的素材',
            48006   => 'api禁止清零调用次数，因为清零次数达到上限',
            50001   => '用户未授权该api',
            50002   => '用户受限，可能是违规后接口被封禁',
            61451   => '参数错误(invalid parameter)',
            61452   => '无效客服账号(invalid kf_account)',
            61453   => '客服帐号已存在(kf_account exsited)',
            61454   => '客服帐号名长度超过限制(仅允许10个英文字符，不包括@及@后的公众号的微信号)(invalid   kf_acount length)',
            61455   => '客服帐号名包含非法字符(仅允许英文+数字)(illegal character in     kf_account)',
            61456   => '客服帐号个数超过限制(10个客服账号)(kf_account count exceeded)',
            61457   => '无效头像文件类型(invalid   file type)',
            61450   => '系统错误(system error)',
            61500   => '日期格式错误',
            65301   => '不存在此menuid对应的个性化菜单',
            65302   => '没有相应的用户',
            65303   => '没有默认菜单，不能创建个性化菜单',
            65304   => 'MatchRule信息为空',
            65305   => '个性化菜单数量受限',
            65306   => '不支持个性化菜单的帐号',
            65307   => '个性化菜单信息为空',
            65308   => '包含没有响应类型的button',
            65309   => '个性化菜单开关处于关闭状态',
            65310   => '填写了省份或城市信息，国家信息不能为空',
            65311   => '填写了城市信息，省份信息不能为空',
            65312   => '不合法的国家信息',
            65313   => '不合法的省份信息',
            65314   => '不合法的城市信息',
            65316   => '该公众号的菜单设置了过多的域名外跳（最多跳转到3个域名的链接）',
            65317   => '不合法的URL',
            9001001 => 'POST数据参数不合法',
            9001002 => '远端服务不可用',
            9001003 => 'Ticket不合法',
            9001004 => '获取摇周边用户信息失败',
            9001005 => '获取商户信息失败',
            9001006 => '获取OpenID失败',
            9001007 => '上传文件缺失',
            9001008 => '上传素材的文件类型不合法',
            9001009 => '上传素材的文件尺寸不合法',
            9001010 => '上传失败',
            9001020 => '帐号不合法',
            9001021 => '已有设备激活率低于50%，不能新增设备',
            9001022 => '设备申请数不合法，必须为大于0的数字',
            9001023 => '已存在审核中的设备ID申请',
            9001024 => '一次查询设备ID数量不能超过50',
            9001025 => '设备ID不合法',
            9001026 => '页面ID不合法',
            9001027 => '页面参数不合法',
            9001028 => '一次删除页面ID数量不能超过10',
            9001029 => '页面已应用在设备中，请先解除应用关系再删除',
            9001030 => '一次查询页面ID数量不能超过',
            9001031 => '时间区间不合法',
            9001032 => '保存设备与页面的绑定关系参数错误',
            9001033 => '门店ID不合法',
            9001034 => '设备备注信息过长',
            9001035 => '设备申请参数不合法',
            9001036 => '查询起始值begin不合法'
        ];
        if ($error[$code]) {// 设置正确但是提交数据错误时
            return $error[$code];
        } else {// 如appId,aesKey等设置错误时,这时候异常的code为0，微信的错误信息被封装在message中，所以需要对message进行处理
            if ($code == 0) {
                // $message = 'Request AccessToken fail. response: {"errcode":40013,"errmsg":"invalid appid hint: [ld07792974]"}'
                $jsonString = str_replace('Request AccessToken fail. response: ','',$message);
                // $jsonString = '{"errcode":40013,"errmsg":"invalid appid hint: [ld07792974]"}'
                $err = json_decode($jsonString,true);
                // $err = { ["errcode"]=> int(40013) ["errmsg"]=> string(32) "invalid appid hint: [ld07792974]" }
                if (!empty($err['errcode'])) { // 避免无限递归
                    return static::getErrorMessage($err['errcode'],$err['errmsg']);
                }
            }
        }
        return '错误代码:'.$code.' 错误信息:'.$message;
    }
}