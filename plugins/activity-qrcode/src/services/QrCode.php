<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/12/4
 * Time: 下午3:55
 */

namespace Yunshop\ActivityQrcode\services;


use app\common\exceptions\ShopException;

class QrCode
{
    private $patch;
    private $url;
    private $fileName;
    private $size = 240;
    private $margin = 10;

    function __construct($url, $patch, $size, $margin)
    {
        $this->patch = $patch;
        $this->url = $url;
        $this->fileName = $this->getFileName();
        $this->size = $size;
        $this->margin = $margin;
    }

    public function url()
    {
        return request()->getSchemeAndHttpHost() . config('app.webPath') . \Storage::url($this->patch."/{$this->fileName}.png");
    }

    private function getFileName()
    {
        $name = md5($this->url);
        if (!is_dir(storage_path($this->patch))) {
            self::directory(storage_path($this->patch));
            mkdir(storage_path($this->patch), 0777);
        }
        if (!is_dir(storage_path($this->patch))) {
            throw new ShopException('生成二维码目录失败');
        }

        if (!file_exists(storage_path($this->patch . "/{$name}.png")) || request()->input('new')) {
            unlink(storage_path($this->patch . "/{$name}.png"));
            // 注意:format方法必须先调用,否则后续方法不生效

            \QrCode::format('png')->size($this->size)->margin($this->margin)->generate($this->url, storage_path($this->patch . "/{$name}.png"));
        }
        if (!file_exists(storage_path($this->patch . "/{$name}.png"))) {
            throw new ShopException('生成二维码失败');
        }
        return $name;
    }

    private function directory($dir)
    {
        return is_dir($dir) or self::directory(dirname($dir)) and mkdir($dir, 0777);
    }

}