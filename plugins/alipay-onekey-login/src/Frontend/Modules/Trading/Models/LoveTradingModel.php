<?php
namespace Yunshop\Love\Frontend\Modules\Trading\Models;
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/7/26
 * Time: 上午9:52
 */
class LoveTradingModel extends \Yunshop\Love\Common\Models\LoveTradingModel
{
    
    public static function getLoveTradings($status = null,$own)
    {
        $model = self::uniacid();

        if($status == '0'){
            $model->where('status',$status);
        }elseif($status == '1'){
            $model->where('status',$status);
            $model->orWhere('status','2');
        }
        if($own){
            $model->where('member_id',\YunShop::app()->getMemberId());
            $model->orWhere('buy_id',\YunShop::app()->getMemberId());
        }
        return $model;
    }
}