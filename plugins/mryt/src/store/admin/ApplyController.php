<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/13
 * Time: 下午3:45
 */

namespace Yunshop\Mryt\store\admin;


use app\backend\modules\member\models\Member;
use app\common\components\BaseController;
use app\common\exceptions\ShopException;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\models\Address;
use app\common\models\MemberShopInfo;
use app\common\models\user\UniAccountUser;
use Yunshop\Mryt\models\MrytMemberModel;
use Yunshop\Mryt\store\common\controller\CommonController;
use Yunshop\Mryt\store\models\Store;
use Yunshop\Mryt\store\models\StoreApply;
use Yunshop\Mryt\store\models\StoreCategory;
use Yunshop\Mryt\models\weiqing\UsersPermission;
use Yunshop\Mryt\models\weiqing\WeiQingUsers;
use Yunshop\Mryt\store\services\MessageService;
use Yunshop\Mryt\store\models\Goods;
use Illuminate\Support\Facades\DB;
use Yunshop\Mryt\store\services\StoreBossService;
use Yunshop\Mryt\store\models\StoreSetting;


class ApplyController extends CommonController
{
    const LIST_VIEW = 'Yunshop\Mryt::store.apply_list';
    const INDEX_URL = 'plugin.mryt.store.admin.apply.index';

    public function index()
    {
        $store = MrytMemberModel::verify(\YunShop::app()->uid);
        if ($store) {
            $ids = MemberShopInfo::uniacid()
                ->where('parent_id', $store->uid)
                ->get(['member_id'])
                ->toArray();
            $child_ids = array_column($ids, 'member_id');
        }
        $list = StoreApply::getStoreApplyList(request()->search)->whereIn('uid', $child_ids)->orderBy('id', 'desc')->paginate(StoreApply::PAGE_SIZE);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        return view(self::LIST_VIEW, [
            'list' => $list,
            'pager' => $pager,
            'search' => request()->search
        ])->render();
    }

    public function examine()
    {
        $storeApplyModel = $this->verification();
        if (request()->type == StoreApply::ADOPT) {
            $this->verifyUsernameAndPassword($storeApplyModel);
            $user_uid = $this->register($storeApplyModel);
            $this->addStore($user_uid, $storeApplyModel);
            $storeApplyModel->save();
            // 审核通过通知
            MessageService::becomeStore($storeApplyModel->uid, $storeApplyModel->uniacid);
        } else {
            $storeApplyModel->delete();
            // 审核驳回通知
            MessageService::rejectStore($storeApplyModel->uid, $storeApplyModel->uniacid);
        }
        return $this->message('审核成功', Url::absoluteWeb(self::INDEX_URL));
    }

    private function verifyUsernameAndPassword($storeApplyModel)
    {
        $result = WeiQingUsers::getUserByUserName($storeApplyModel->username)->first();
        if ($result) {
            throw new ShopException('此用户为系统存在用户');
        }
    }


    private function register($storeApplyModel)
    {
        $user_uid = user_register(array('username' => $storeApplyModel->username, 'password' => $storeApplyModel->password));

        WeiQingUsers::updateType($user_uid);

        // 公众号权限
        $uni_model = new UniAccountUser();
        $uni_model->fill([
            'uid'       => $user_uid,
            'uniacid'   => \YunShop::app()->uniacid,
            'role'      => 'clerk'
        ]);
        $uni_model->save();

        // 模块权限
        (new UsersPermission())->addPermission($user_uid);

        return $user_uid;
    }

    private function addStore($user_uid, $storeApplyModel)
    {

        $goods_model = Goods::saveGoods([
            'store_name' => $storeApplyModel->realname,
            'thumb' => $storeApplyModel->information['thumb'],
            'type' => 2
        ], Goods::getWidgets());

        $storeData = [
            'uniacid' => $storeApplyModel->uniacid,
            'store_name' => $storeApplyModel->information['store_name'],
            'thumb' => $storeApplyModel->information['thumb'],
            'uid' => $storeApplyModel->uid,
            'user_uid' => $user_uid,
            'category_id' => $storeApplyModel->information['category_id'],
            'province_id' => $storeApplyModel->information['province_id'],
            'city_id' => $storeApplyModel->information['city_id'],
            'district_id' => $storeApplyModel->information['district_id'],
            'street_id' => $storeApplyModel->information['street_id'],
            'address' => $storeApplyModel->information['address'],
            'longitude' => $storeApplyModel->information['lng'],
            'latitude' => $storeApplyModel->information['lat'],
            'mobile' => $storeApplyModel->mobile,
            'store_introduce' => $storeApplyModel->information['remark'],
            'cashier_id' => $goods_model->id,
            'aptitude_imgs' => $storeApplyModel->information['aptitude_imgs'],
            'business_hours_start' => $storeApplyModel->information['business_hours_start'],
            'business_hours_end' => $storeApplyModel->information['business_hours_end'],
            'dispatch_type' => [1],
            'validity' => $storeApplyModel->validity
        ];
        $set = \Setting::get('plugin.store');
        if (!$storeApplyModel->validity && $set['enter_time_limit'] == 1) {
            $storeData['validity_status'] = 1;
        }

        $address = Address::where('id', $storeData['city_id'])->first();
        if ($address) {
            $storeData['initials'] = Store::getFirstCharter($address->areaname);
        }

        if ($storeData) {
            $this->verifyUsernameAndPassword($storeData);
            DB::transaction(function () use ($storeData,$goods_model) {
                $verify_res = Store::verifyStore(Store::getStoreData($goods_model, $storeData));
                $this->saveStore($verify_res);
            });

        }
//        Store::create($storeData);
    }

    private function verification()
    {
        $applyId = intval(request()->apply_id);
        $type = request()->type;
        if (!$applyId || !in_array($type, [StoreApply::REJECT, StoreApply::ADOPT])) {
            throw new ShopException('参数错误');
        }
        $storeApplyModel = StoreApply::find($applyId);
        if (!$storeApplyModel) {
            throw new ShopException('未找到申请记录');
        }
        if ($storeApplyModel->status == $type) {
            throw new ShopException('该申请已审核，请勿重复审核');
        }
        $storeApplyModel->status = $type;
        return $storeApplyModel;
    }


    public function detail()
    {
        $set = \Setting::get('plugin.store');
        $apply_id = intval(request()->id);
        if (!$apply_id) {
            throw new ShopException('参数错误');
        }
        $apply_model = StoreApply::getStoreApplyById($apply_id)->first();
        if (!$apply_model) {
            throw new ShopException('未找到门店申请数据');
        }
        $category_list = StoreCategory::getList()->get();

        return view('Yunshop\Mryt::store.apply_detail', [
            'apply' => $apply_model,
            'category_list' => $category_list,
            'set' => $set
        ])->render();
    }

    private function saveStore($verify_res)
    {

            if ($this->store_uid != $verify_res['store_model']->uid) {
                $apply = StoreApply::select(['id', 'uid'])
                    ->where('uid', $this->store_uid)->first();
                if ($apply) {
                    $apply->delete();
                }
            }

            $verify_res['store_model']->save();
            $this->storeId = $verify_res['store_model']->id;

            // 连锁店
//            (new StoreBossService($this->storeId, $this->boss_uid, $verify_res['store_model']))->handle();
            StoreSetting::setStoreSetting($this->storeId);

            if (!$verify_res['store_model']->user_uid) {
                $this->register($verify_res['store_model']);
            }

    }


}