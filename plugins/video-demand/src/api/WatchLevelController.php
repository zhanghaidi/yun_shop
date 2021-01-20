<?php
/**
 * Date: 2018/2/22
 * Time: 上午10:53
 */

namespace Yunshop\VideoDemand\api;

use app\common\components\ApiController;
use app\backend\modules\member\models\MemberLevel;
use Yunshop\VideoDemand\models\CourseGoodsModel;



class WatchLevelController extends ApiController
{
    /**
     * 等级信息
     * @return [json]
     */
    public function index()
    {
        //会员等级的升级的规则
        $settinglevel = \Setting::get('shop.member');

        //获取课程id
        $id = \Yunshop::request()->course_id;

        if (!$settinglevel || $settinglevel['level_type'] != 2) {
            return $this->errorJson('未进行等级设置');
        }

        $level_ids = CourseGoodsModel::where('id', $id)->value('see_levels');
        $level_ids = explode(',', $level_ids);
        $levelModel = MemberLevel::select('level_name','goods_id')->whereIn('id', $level_ids)->uniacid()
            ->with(['goods' => function($query) {
                return $query->select('id','title','thumb','price');
            }])->orderBy('level')->get()->toArray();;
        
        if (empty($levelModel)) {
            return $this->errorJson('无等级设置');
        }

        foreach ($levelModel as &$value) {
            $value['goods']['thumb'] = replace_yunshop(yz_tomedia($value['goods']['thumb'])); 
        }

        $data = [
            'level_type' => 2,
            'data' => $levelModel,
        ];


        $this->successJson('ok', $data);

    }
}