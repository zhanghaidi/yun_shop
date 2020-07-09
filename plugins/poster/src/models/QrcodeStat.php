<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;

class QrcodeStat extends BaseModel
{
    protected $table = 'qrcode_stat';
    protected $guarded = [''];
    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (config('APP_Framework') == 'platform') {
            $this->table = 'yz_qrcode_stat';
        } else {
            $this->table = 'qrcode_stat';
        }
    }

    /**
     * 多个扫码记录属于一个二维码
     */
    public function qrcode()
    {
        return $this->belongsTo('YunShop\Poster\models\Qrcode', 'qid', 'id');
    }

}