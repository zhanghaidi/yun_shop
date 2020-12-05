<?php
/**
 * Created by PhpStorm.
 *
 * User: king/QQ：995265288
 * Date: 2018/3/5 下午2:50
 * Email: livsyitian@163.com
 */

namespace Yunshop\Circle\Backend\Controllers;


use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\Url;
use Yunshop\Circle\Common\Services\SetService;

class BaseSetController extends BaseController
{
    protected $success_url = 'plugin.sign.Backend.Controllers.base-set.see';

    protected $view_value = 'Yunshop\Sign::Backend.base_set';

    public function see()
    {
        dd('Circle');
        //dd(SetService::getSignSet()['cumulative']);
        return view($this->view_value,['sign' => SetService::getSignSet()])->render();
    }

    public function store()
    {
        $requestData = \YunShop::request()->sign;


        if (isset($requestData['cumulative'])) {
            $requestData['cumulative'] = $this->cumulativeAwardData($requestData['cumulative']);
        } else {
            $requestData['cumulative'] = [];
        }

        //dd($requestData);

        if ($requestData) {
            $result = SetService::storeSet($requestData);
            if ($result === true) {
                return $this->message("设置保存成功",Url::absoluteWeb($this->success_url));
            }
            $this->error($result);
        }
        return $this->see();
    }

    public function cumulativeAwardData($cumulative)
    {
        //dd($cumulative);
        $array = [];
        $cumulative['award_type'] = is_array($cumulative['award_type']) ? $cumulative['award_type'] : array();

        foreach ($cumulative['award_type'] as $key => $value) {
            if (trim($value)) {
                $array[] = array(
                    'award_type' => trim($cumulative['award_type'][$key]),
                    'days' => trim($cumulative['days'][$key]),
                    'coupon_id' => trim($cumulative['coupon_id'][$key]),
                    'coupon_name' => trim($cumulative['coupon_name'][$key]),
                    'award_value' => trim($cumulative['award_value'][$key]),
                );
            }
        }
        foreach ($array as $key => $item) {
            $this->validatorCustomRules($item, $this->rules(), '', $this->attributes());
        }
        return $array;
    }


    private function validatorCustomRules($array,$rules,$messages,$customAttributes)
    {
       // dd($array,$rules,$messages,$customAttributes);
        $validator = $this->getValidationFactory()->make($array, $rules, [], $customAttributes);

        if ($validator->fails()) {
            throw new ShopException($validator->errors()->first());
        }
    }


    private function rules()
    {
        return [
            'award_type'        => 'numeric|min:0',
            'days'              => 'numeric|min:0',
            'coupon_id'         => 'numeric|min:0',
            //'coupon_name'       => 'numeric|min:0',
            'award_value'       => 'numeric|min:0',
        ];
    }

    private function attributes()
    {
        return [
            'award_type'        => '连签奖励类型',
            'days'              => '连签奖励天数',
            'coupon_id'         => '连签奖励优惠券ID',
            //'coupon_name'       => '连签奖励优惠券名称',
            'award_value'       => '连签奖励值',
        ];
    }

}
