<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class Qrcode extends BaseModel
{
    protected $table = 'qrcode';
    protected $guarded = [''];
    public $timestamps = false;

    const MAX_FOREVER_QRCODE_LIMIT = 100000; //微信对"永久二维码"的总数有限制, 必须在100000以内
    const TEMPORARY_QRCODE = 1; //临时二维码
    const FOREVER_QRCODE = 2; //永久二维码

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (config('APP_Framework') == 'platform') {
            $this->table = 'yz_qrcode';
        } else {
            $this->table = 'qrcode';
        }
    }

    /**
     * 一个二维码对应多个扫码记录
     */
    public function qrcodeStat()
    {
        return $this->hasMany('YunShop\Poster\models\QrcodeStat', 'qid', 'id');
    }

    //插入数据, 并获得ID
    //$attributes 数组
    public static function createAndGetInsertId($attributes)
    {
        $qrcode = self::create($attributes);
        return $qrcode->id;
    }

    //根据id获取二维码模型
    public static function getQrcodeById($id)
    {
        $qrcode = self::find($id);
        return $qrcode;
    }

    //根据场景值获取二维码 (可以是临时二维码或者是永久二维码)
    public static function getQrcodeBySceneId($sceneId)
    {
        $qrcode = self::uniacid()
            ->where('qrcid', '=', $sceneId)
            ->first();
        return $qrcode;
    }

    //根据场景字符串获取永久二维码
    public static function getForeverQrcodeBySceneStr($sceneStr)
    {
        $qrcode = static::uniacid()
            ->where('model', '=', 2)
            ->where('scene_str', '=', $sceneStr)
            ->first();
        return $qrcode;
    }

    //获取已生成的临时二维码的最大场景值
    public static function getMaxSceneIdofTempQrcode()
    {
        $maxQrcodeId = self::uniacid()
                        ->where('model', '=', self::TEMPORARY_QRCODE)
                        ->max('qrcid');
        return $maxQrcodeId;
    }

    //获取已生成的永久二维码的总数
    public static function getSumOfForeverQrcode()
    {
        $sum = self::uniacid()
                    ->where('model', '=', self::FOREVER_QRCODE)
                    ->count();
        return $sum;
    }


}