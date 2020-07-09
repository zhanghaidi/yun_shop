<?php


namespace Yunshop\JdSupply\frontend\goods;


use app\common\components\ApiController;
use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\models\Address;
use app\common\models\Order;
use app\common\models\Street;
use app\frontend\repositories\MemberAddressRepository;
use Yunshop\JdSupply\models\GoodsOption;
use Yunshop\JdSupply\models\JdGoodsOption;
use Yunshop\JdSupply\services\JdOrderService;
use Yunshop\JdSupply\services\sdk\JdNewClient;
use Yunshop\JdSupply\services\sdk\JdNewRequest;

class OptionController extends BaseController
{
    protected $order;

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws AppException
     */
    public function index()
    {
        $goods_id = request()->goods_id;
        $set = \Setting::get('plugin.jd_supply');
        $jd_option = JdGoodsOption::GoodsId($goods_id)->get();
        $spu = $jd_option->map(function ($item) {
            if (empty($item->jd_option_id)) {
                return false;
            }
            $goods['sku'] = $item->jd_option_id;
            $goods['number'] = 1;
            return $goods;
        })->toArray();
        $address = app(MemberAddressRepository::class)->makeModel();
        $memberId = \YunShop::app()->getMemberId();
        $addressList = $address::select('id', 'username', 'mobile', 'zipcode', 'province', 'city', 'district', 'address', 'isdefault')
            ->uniacid()
            ->where('uid', $memberId)
            ->where('isdefault',1)
            ->first();
        $post['spu'] = array_filter($spu);
        $post['address'] = $this->getMemberAddress($addressList);
        $request = new JdNewRequest($set['app_secret'], $set['app_key']);
        $request->addBody(json_encode($post));

        $response = JdNewClient::getBody('/v2/order/availableCheck', $request);
        $data =  json_decode($response, true);
        if ($data['code'] == 1) {
            $check_result = $data['data']['data'];
            foreach ($jd_option as $key=>$val) {
                if (in_array($val->jd_option_id,$check_result['available'])) {
                    $result[$val->option_id] = 1;
                } else {
                    $result[$val->option_id] = 0;
                }
            }
        } else {
            \Log::debug('聚合详情页校验',$data);
            $option = \app\frontend\models\GoodsOption::where('goods_id',$goods_id)->get();
            foreach ($option as $key=>$val) {
                $result[$val->id] = empty($val->stock)?0:1;
            }
        }
        return $this->successJson('',$result);
    }

    public function getMemberAddress($address)
    {
        $order_data['consignee'] = $address->username?:'匿名';
        $order_data['phone'] = $address->mobile;
        $order_data['province'] = $address->province;
        $order_data['city'] = $address->city;
        $order_data['area'] = $address->district;
        $order_data['street'] = $address->street?:'其他';
        $order_data['description'] =  trim(last(explode(' ', $address->address)));
        return $order_data;
    }


}