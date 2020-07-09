<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/24
 * Time: 11:54
 */

namespace Yunshop\JdSupply\admin;


use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\JdSupply\models\Order;
use Yunshop\JdSupply\services\CreateOrderService;
use Yunshop\JdSupply\services\JdGoodsService;

class SetController extends BaseController
{
    public function index()
    {
        $set = \Setting::get('plugin.jd_supply');
        if (request()->isMethod('post')) {
            $set_data = request()->input('set');
            if (!isset($set_data['app_secret'])) {
                $set_data['app_secret'] = $set['app_secret'];
            }
            \Setting::set('plugin.jd_supply', $set_data);
            return $this->message('保存成功', Url::absoluteWeb('plugin.jd-supply.admin.set.index'));
        }
        $push_url = Url::shopSchemeUrl('plugins/jd-supply/api/index.php?i='.\YunShop::app()->uniacid);
        return view('Yunshop\JdSupply::admin.set', [
            'set' => $set,
            'push_url'=>$push_url
        ])->render();

    }

}