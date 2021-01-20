<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/17
 * Time: 19:26
 */

namespace Yunshop\HelpUserBuying\admin;

use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use app\common\models\Address;
use app\common\models\Member;
use app\common\models\MemberAddress;
use app\common\models\Street;
use app\common\models\YzMemberAddress;
use app\common\services\Session;
use app\backend\modules\goods\services\CategoryService;
use Yunshop\HelpUserBuying\models\Goods;
use Yunshop\HelpUserBuying\store\models\Store;
use Yunshop\HelpUserBuying\store\models\StoreGoods;

class HomeController extends BaseController
{

    protected $store_id;


    protected $plugin_id = [0];

    public function shopIndex()
    {

        $member_id = $this->setMemberId();

        $member = Member::getMemberById($member_id);

        $member_address = $this->getMemberAddress($member_id);

        $catetory_menus = CategoryService::getCategoryMultiMenuSearch(['catlevel' => \Setting::get('shop.category')['cat_level']]);


        if (app('plugins')->isEnabled('supplier')) {
            $this->plugin_id[] = 92;
        }

        if (app('plugins')->isEnabled('net-car')) {
            $this->plugin_id[] = 41;
        }

        $list =  Goods::where('stock', '>', 0)->where('status', 1)->whereIn('plugin_id', $this->plugin_id)
            ->with('hasManyGoodsCategory')->get();

        if ($list->isEmpty()) {
            throw new ShopException('无商品');
        }

        $list = $this->goodsMap($list, 'shop');

        return view('Yunshop\HelpUserBuying::admin.index', [
            'goodsList' => $list->toArray(),
            'catetory_menus' => $catetory_menus,
            'store' => [],
            'member' => $member,
            'member_address' => $member_address,
            'route_url' => yzWebUrl('plugin.help-user-buying.admin.home.shop-index'),
            'order_url' => [
                'create_url' => yzWebUrl('plugin.help-user-buying.shop.controller.create.index'),
                'pre_url' => yzWebUrl('plugin.help-user-buying.shop.controller.goods-buy.index'),
            ],
        ]);
    }

    public function storeIndex()
    {

        $member_id = $this->setMemberId();


        $store_id = intval(request()->input('store_id'));


        $member = Member::getMemberById($member_id);

        $member_address = $this->getMemberAddress($member_id);


        $store = Store::uniacid()->whereId($store_id)->first();

        //$is_goods = StoreGoods::whereStoreId($store_id)->get()->toArray();

        $catetory_menus = \Yunshop\StoreCashier\common\services\CategoryService::getCategoryMenu(
            [
                'store_id' => $store_id,
                'catlevel' => 2,
                'ids'   => [],
            ]
        );

        $list =  StoreGoods::getGoodsList([], $this->store_id)->pluginId()->where('stock', '>', 0)->where('status', 1)
            ->with('hasOneCategory')->where('stock', '>', 0)->get();

        if ($list->isEmpty()) {
            throw new ShopException('无商品');
        }

        $list = $this->goodsMap($list, 'store');

        return view('Yunshop\HelpUserBuying::admin.index', [
            'goodsList' => $list->toArray(),
            'catetory_menus' => $catetory_menus,
            'store' => $store,
            'member' => $member,
            'member_address' => $member_address,
            'route_url' => yzWebUrl('plugin.help-user-buying.admin.home.store-index'),
            'order_url' => [
                'create_url' => yzWebUrl('plugin.help-user-buying.store.controller.create.index'),
                'pre_url' => yzWebUrl('plugin.help-user-buying.store.controller.goods-buy.index'),
            ],
        ]);
    }

    protected function goodsMap($list, $style = 'shop')
    {
        $list->map(function($goods) use ($style) {
            $goods->thumb = yz_tomedia($goods->thumb);

            if ($style == 'store') {
                $goods->category_ids = $goods->hasOneCategory->category_ids;
            } elseif ($style == 'shop') {
                $goods->category_ids =  $goods->hasManyGoodsCategory->implode('category_ids', ',');
            }

        });
        return $list;
    }


    protected function setMemberId()
    {
        $member_id = intval(request()->input('uid'));

        if (empty($member_id)) {
            throw new ShopException('用户不存在');
        }

        Session::set('member_id', $member_id);

        return $member_id;
    }

    public function getMemberAddress($uid)
    {
        if(\Setting::get('shop.trade.is_street')) {
            $member_address = YzMemberAddress::uniacid()->whereUid($uid)->where('isdefault',1)->first();
            if ($member_address) {
                $member_address->province_id = Address::where('areaname',$member_address->province)->value('id');
                $member_address->city_id = Address::where('areaname',$member_address->city)->value('id');
                $member_address->district_id = Address::where('areaname',$member_address->district)->where('parentid', $member_address->city_id)->value('id');
                $member_address->street_id = Street::where('parentid', $member_address->district_id)->where('areaname',$member_address->street)->value('id');
            }
        } else{
            $member_address = MemberAddress::uniacid()->whereUid($uid)->where('isdefault',1)->first();

            if ($member_address) {
                $member_address->province_id = Address::where('areaname',$member_address->province)->value('id');
                $member_address->city_id = Address::where('areaname',$member_address->city)->value('id');
                $member_address->district_id = Address::where('areaname',$member_address->district)->where('parentid', $member_address->city)->value('id');
            }
        }


        return !empty($member_address) ? $member_address : [];
    }

    public function select()
    {

        if (app('plugins')->isEnabled('store-cashier')) {
            $store = \Yunshop\StoreCashier\common\models\Store::uniacid()->get()->toArray();
        }

        //预防万一先清空会员的member_id Session
        Session::clear('member_id');

        return view('Yunshop\HelpUserBuying::admin.select',[
            'store' => isset($store)?$store:[],
            ])->render();
    }

    public function goodsIncrease()
    {
        $id = intval(\YunShop::request()->id);
        $num = intval(\YunShop::request()->num);
        $type = intval(\YunShop::request()->type);
        $goods = Goods::where('status', 1)->find($id);

        if (empty($goods)) {
            return $this->errorJson('商品不存在或以下架',['code'=> 2]);
        }

        $new_num = $type ? $num : $num + 1;

        if ($goods->stock < $new_num) {
            return $this->successJson('最大',['data' => $goods->stock]);
        }


        return $this->successJson('ok',['data' => $new_num]);
    }

}