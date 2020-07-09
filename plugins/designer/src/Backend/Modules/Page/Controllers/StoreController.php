<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/9/17
 * Time: 上午10:47
 */

namespace Yunshop\Designer\Backend\Modules\Page\Controllers;


use app\common\components\BaseController;
use Yunshop\Designer\Backend\Models\Designer;
use Yunshop\Designer\Backend\Models\DesignerMenu;

class StoreController extends BaseController
{
    public function index()
    {

    }



    private function defaultPageInfo()
    {
        return [
            'id' => 'M0000000000000',
            'temp' => 'topbar',
            'params' => [
                'title'=> '',
                'desc' => '',
                'img' => '',
                'kw' => '',
                'footer' => '1',
                'footermenu' => '{$defaultmenuid}',
                'floatico' => 0,
                'floatstyle' => 'right',
                'floatwidth' => '40px',
                'floattop' => '100px',
                'floatimg' => '',
                'floatlink' => ''
            ]
        ];
    }

}
