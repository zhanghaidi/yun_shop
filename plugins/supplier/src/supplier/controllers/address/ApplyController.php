<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/25
 * Time: 10:46
 */

namespace Yunshop\Supplier\supplier\controllers\address;


use app\common\models\Address;
use Yunshop\RegionMgt\models\AddressChange;
use Yunshop\RegionMgt\models\Region;
use Yunshop\Supplier\common\controllers\SupplierCommonController;
use Yunshop\Supplier\common\models\Supplier;
use app\common\exceptions\ShopException;
use app\common\services\Session;
use app\common\helpers\Url;
use Setting;

class ApplyController extends SupplierCommonController
{
    public function __construct()
    {
        parent::__construct();
        $this->supplier_id = Session::get('supplier')['id'];
    }

    public function index()
    {
        if (!app('plugins')->isEnabled('region-mgt')) {
            return $this->message('请先开启区域管理插件', Url::absoluteWeb('plugin.supplier.supplier.controllers.order.supplier-order.index'));
        }
        $change_model = AddressChange::uniacid()->suppId($this->supplier_id)->status()->first();

        if (request()->isMethod('post')) {
            $data = request()->input('data');

            if ($change_model) {
                throw new ShopException('已存在申请，未审核的更改信息');
            }

            if (empty($data['district_id']) && $data['city_id']) {
                $data['region_level'] = 2;
            } elseif (!empty($data['district_id'])) {
                $data['region_level'] = 3;
            } elseif (empty($data['district_id']) && empty($data['city_id']) && $data['province_id']) {
                $data['region_level'] = 1;
            }
            $region_id = Region::verifyRegion($data);
            if (!$region_id) {
                throw new ShopException('所选区域暂无管理者');
            }
            $supp =  Supplier::getSupplierById($this->supplier_id);

            $data['current_address'] = "{$supp['province_name']} {$supp['city_name']} {$supp['district_name']}";
            $data['uniacid'] = \YunShop::app()->uniacid;
            $data['supplier_id'] = $supp->id;
            $data['region_id'] = $region_id;
            $data = $this->getAddressName($data);

            AddressChange::insertData($data);

            return $this->message('申请成功', Url::absoluteWeb('plugin.supplier.supplier.controllers.address.apply.index'));
        }


        return view('Yunshop\Supplier::supplier.address.apply', [
            'data' => $change_model,
        ])->render();
    }
    
    public function getAddressName($address)
    {
        $address['province_name'] = Address::where('id',$address['province_id'])->value('areaname');
        $address['city_name'] = Address::where('id',$address['city_id'])->value('areaname');
        if ($address['region_level'] == 3) {
            $address['district_name'] = Address::where('id',$address['district_id'])->value('areaname');
        }

        return $address;
    }
}