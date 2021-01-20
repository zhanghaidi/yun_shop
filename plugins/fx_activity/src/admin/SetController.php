<?php
/**
 * Created by PhpStorm.
 * User: yunzhong
 * Date: 2018/3/19
 * Time: 9:57
 */
namespace Yunshop\FxActivity\admin;

use app\common\components\BaseController;
use app\backend\modules\member\models\MemberLevel;
use Illuminate\Support\Facades\DB;
use Yunshop\AreaDividend\models\AreaDividendGoods;
use Yunshop\Commission\models\AgentLevel;
use Yunshop\Commission\models\Commission;
use Yunshop\Love\Common\Models\GoodsLove;
use Yunshop\Merchant\common\models\MerchantGoods;
use Yunshop\Merchant\common\models\MerchantLevel;
use Yunshop\SingleReturn\models\SingleReturnGoodsModel;
use Yunshop\TeamDividend\models\GoodsTeamDividend;
use Yunshop\FxActivity\models\BasisSetting;
use Yunshop\FxActivity\models\Goods;

class SetController extends BaseController
{

    protected $setting;

    public function __construct()
    {
        $this->setting = new BasisSetting();
    }

    public function edit()
    {
        $post_data = request()->except('c','a','m','do','route');//去掉不相关数据
        $widgets_data = $post_data['widgets'];

        //判断在设置里是否存在good_id
        $goods_id = $this->setting->getGoodsId();

        //goods_model为空，新建goods_model
        $goods_model = $goods_id ? Goods::find($goods_id) : '';

        //判断是否存在提交数据
        if ($widgets_data) {
            //插入虚拟商品
            $goods_model = Goods::saveGoods($widgets_data, $goods_model);
            $this->setting->saveSetting($widgets_data,$goods_model->id);
            $this->success('保存成功');
        }
        return $this->returnView($goods_model->id);
    }

    private function returnView($goods_id = null)
    {
        $levels = MemberLevel::getMemberLevelList();//获取会员等级
        $data = [
            'levels'        => $levels,
            'exist_plugins' => $this->getPlugins($goods_id),//获取插件设置
            'route'         => request()->route,
        ];
        $data['exist_plugins']['member'] = $this->setting->getMemberPoint();//获取积分
        $full_return = $this->setting->getFullReturn();//获取满额返现
        $data['exist_plugins']['full-return'] = $full_return['full_return'];
        return view('Yunshop\FxActivity::admin.set', $data);
    }

    private function getPlugins($goods_id = '')
    {
        $exist_plugins = [];
        // todo 分销
        $exist_commission = app('plugins')->isEnabled('commission');
        if ($exist_commission) {
            $commission_levels = AgentLevel::getLevels()->get();
            $exist_plugins['commission'] = [
                'commission'    => true,
                'commission_levels' => $commission_levels
            ];
            if ($goods_id) {
                $item = Commission::getModel($goods_id, '');
                $item->rule = unserialize($item->rule);
                $exist_plugins['commission']['commission_goods'] = $item;
            }
        }

        // todo 消费赠送
        $exist_single_return = app('plugins')->isEnabled('single-return');
        if ($exist_single_return) {
            $exist_plugins['single-return']['single_return_goods'] = '';
            if ($goods_id) {
                $item = SingleReturnGoodsModel::getModel($goods_id, '');
                $exist_plugins['single-return']['single_return_goods'] = $item;
            }
        }

        // todo 团队分红
        $exist_team_dividend = app('plugins')->isEnabled('team-dividend');
        if ($exist_team_dividend) {
            $exist_plugins['team-dividend']['team_dividend_goods'] = '';
            if ($goods_id) {
                $item = GoodsTeamDividend::getModel($goods_id,'');
                $exist_plugins['team-dividend']['team_dividend_goods'] = $item;
            }
        }

        // todo 区域分红
        $exist_area_dividend = app('plugins')->isEnabled('area-dividend');
        if ($exist_area_dividend) {
            $exist_plugins['area-dividend']['area_dividend_goods'] = '';
            if ($goods_id) {
                $item = AreaDividendGoods::getModel($goods_id,'');
                $exist_plugins['area-dividend']['area_dividend_goods'] = $item;
            }
        }

        // todo 爱心值
        $exist_love = app('plugins')->isEnabled('love');
        if ($exist_love) {
            $exist_plugins['love']['love_goods'] = '';
            if ($goods_id) {
                $item = GoodsLove::ofGoodsId($goods_id)->first();
                $exist_plugins['love']['love_goods'] = $item;
            }
            $exist_plugins['love']['name'] = \Yunshop\Love\Common\Services\SetService::getLoveName();
        }

        // todo 招商员
        if (app('plugins')->isEnabled('merchant')) {
            $exist_plugins['merchant'] = true;
            $exist_plugins['merchant_levels'] = MerchantLevel::getLevelList()->get();
            if ($goods_id) {
                $item = MerchantGoods::getModelByGoodsId($goods_id)->first();
                $item->level = unserialize($item->set);
                $exist_plugins['merchant'] = $item;
//                dd($exist_plugins['merchant']);
            }
        }

        return $exist_plugins;
    }
}