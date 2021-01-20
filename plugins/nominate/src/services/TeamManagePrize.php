<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2019/1/18
 * Time: 10:45 AM
 */

namespace Yunshop\Nominate\services;


use Yunshop\Nominate\models\MemberParent;
use Yunshop\Nominate\models\NominateGoods;
use Yunshop\Nominate\models\ShopMember;
use Yunshop\Nominate\models\ShopMemberLevel;
use Yunshop\Nominate\models\TeamPrize;

class TeamManagePrize
{
    private $order;
    private $orderGoods;
    private $parents;

    //private $teamManagePrizeRatio = 0;
    private $awardHierarchy = 0;
    private $awardNum = 0;
    private $levelWeight;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        $parents = $this->getParents();
        if ($parents->isEmpty()) {

            //file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'orderid['.$this->order->id.']uid['.$this->order->uid.']没有上级'.PHP_EOL,1), FILE_APPEND);

            return;
        }
        $this->levelWeight = $this->getLevelWeightMin();
        $this->parents = $parents;
        foreach ($this->order->orderGoods as $orderGoods) {
            $nominateGoods = NominateGoods::select()
                ->where('goods_id', $orderGoods->goods_id)
                ->first();
            if (!$nominateGoods || !$nominateGoods->is_open) {

                //file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'orderid['.$this->order->id.']goods_id['.$orderGoods->goods_id.']没开启推荐奖励'.PHP_EOL,1), FILE_APPEND);

                continue;
            }
            $this->orderGoods = $orderGoods;

            $this->award();
        }
    }

    private function award()
    {
        $levelWeight = $this->getLevelWeightMin();
        foreach ($this->parents as $hierarchy => $parent) {
            if ($this->awardNum >= 2) {
                break;
            }

            if (($this->awardHierarchy + 1) != $hierarchy && $this->awardNum > 0) {
                $this->awardNum += 1;
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]层级错误");
                continue;
            }

            $parentModel = ShopMember::select(['member_id', 'level_id', 'parent_id'])
                ->with(['shopMemberLevel'])
                ->whereHas('shopMemberLevel')
                ->where('member_id', $parent->parent_id)
                ->first();
            if (!$parentModel) {
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]没会员");
                break;
            }
            if ($parentModel->shopMemberLevel->level <= $levelWeight) {
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]等级权重最小");
                //$this->awardNum += 1;
                continue;
            }
            // 等级推荐奖励设置
            $nominateLevel = $parentModel->shopMemberLevel->nominateLevel;
            if (!$nominateLevel) {
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]没设置推荐等级");
                break;
            }
            // 团队业绩比例
            if ($nominateLevel->team_manage_prize <= 0) {
                $this->awardNum += 1;
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]团队业绩比例0");
                continue;
            }
            // 比例 = 团队业绩比例
            $ratio = $nominateLevel->team_manage_prize;
            // 奖励金额
            $amount = $this->orderGoods->payment_amount * $ratio / 100;
            if ($amount <= 0) {
                //dump("uid[{$parent->parent_id}]goodsid[{$this->orderGoods->goods_id}]奖励金额0");
                $this->awardNum += 1;
                continue;
            }
            $this->awardHierarchy = $hierarchy;
            $this->awardNum += 1;
            // 存入数据表
            TeamPrize::store([
                'uniacid' => $this->order->uniacid,
                'uid' => $parentModel->member_id,
                'level_id' => $parentModel->level_id,
                'order_id' => $this->order->id,
                'goods_id' => $this->orderGoods->goods_id,
                'ratio' => $ratio,
                'amount' => $amount,
                'status' => 0
            ]);
        }
        // 多商品:第二个商品重新开始
        $this->awardNum = 0;
    }

    private function getLevelWeightMin()
    {
        $levelModel = ShopMemberLevel::select(['id', 'level', 'level_name'])
            ->where('uniacid', $this->order->uniacid)
            ->orderBy('level', 'asc')
            ->first();
        return intval($levelModel->level);
    }

    private function getParents()
    {
        \YunShop::app()->uniacid = $this->order->uniacid;
        /*if (!\YunShop::app()->uniacid) {
            file_put_contents(storage_path('logs/zxz.txt'), print_r(date('Ymd His').'orderid['.$this->order->id.']uid['.$this->order->uid.']公众号错误'.PHP_EOL,1), FILE_APPEND);
        }*/
        $parents = MemberParent::select()
            ->where('member_id', $this->order->uid)
            ->orderBy('level', 'asc')
            ->get();
        $self = new MemberParent();
        $self->fill([
            'uniacid' => $this->order->uniacid,
            'parent_id' => $this->order->uid,
            'level' => 0,
            'member_id' => $this->order->uid
        ]);

        return $parents->prepend($self);
    }
}