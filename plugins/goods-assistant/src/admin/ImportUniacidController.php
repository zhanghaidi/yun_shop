<?php

namespace Yunshop\GoodsAssistant\admin;

use app\common\components\BaseController;
use app\platform\modules\application\models\AppUser;
use app\platform\modules\application\models\UniacidApp;
use app\platform\modules\user\models\AdminUser;
use Illuminate\Support\Facades\DB;
use app\common\models\UniAccount;

class ImportUniacidController extends BaseController
{
    /**
     * 被导入公众号
     * @var int
     */
    private $uniacids = 0;

    /**
     * 当前公众号
     * @var int
     */
    private $uniacid = 0;

    /**
     * key    被导入公众号的goods_id
     * value 为新旧goods_id
     * @var array
     */
    private $goodsListId = array();

    /**
     * 被导入公众号的所有goods_id
     * @var array
     */
    private $oldGoodsListId = array();

    /**
     * 定义调用的方法
     * @var array
     */
    private $func = [
        'importGoods',
        'importGoodsCategory',
        'importGoodsSale',
        'importGoodsVideo',
        'importGoodsDiscount',
        'importGoodsDispatch',
        'importGoodsParam',
        'importGoodsSpec',
    ];

    //导入页面
    public function index()
    {
        $uniAccount = UniAccount::where('uniacid', '!=', \YunShop::app()->uniacid)->get();
        if (config('APP_Framework') == 'platform') {
            $list = $this->platform();
        }else{
            $list = $this->weiqing();
        }
        return view('Yunshop\GoodsAssistant::admin.importUniacid', ['uniAccount' => $list])->render();
    }

    /**
     * 获取 公众号列表
     * @return array
     */
    private function weiqing()
    {
        $account_table = table('account');

        $account_table->searchWithType(array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH));
        $account_count = $account_table->searchAccountList();
        $total = count($account_count);
        $account_table->searchWithType(array(ACCOUNT_TYPE_OFFCIAL_NORMAL, ACCOUNT_TYPE_OFFCIAL_AUTH));


        $letter = $_GPC['letter'];
        if(isset($letter) && strlen($letter) == 1) {
            $account_table->searchWithLetter($letter);
        }

        $account_table->accountRankOrder();
        $account_list = $account_table->searchAccountList();

        $account_list = array_values($account_list);
        foreach($account_list as $key => &$account) {
            if($account['uniacid'] == \YunShop::app()->uniacid){
                   unset($account_list[$key]);
            }
            $account = uni_fetch($account['uniacid']);
            $account['role'] = permission_account_user_role($_W['uid'], $account['uniacid']);
        }
        return $account_list;
    }

    /**
     * 获取公众号列表
     * @return array|\Illuminate\Http\JsonResponse
     */
    private function platform()
    {
        $app = new UniacidApp();
        $ids =  \app\platform\modules\application\controllers\ApplicationController::checkRole();
        if (\Auth::guard('admin')->user()->uid != 1) {
            if (!is_array($ids)) {
                return [];
            }
            $list = $app->whereIn('id', $ids)->where('uniacid','!=',\YunShop::app()->uniacid)->where('status', 1)->orderBy('id', 'desc')->get();
            if($list){
                $list = $list->toArray();
            }
        } else {
            $list = $app->where('status', 1)->where('uniacid','!=',\YunShop::app()->uniacid)->orderBy('id', 'desc')->get();
            if($list){
                $list = $list->toArray();
            }
        }

        foreach ($list as $key => $value) {
            if ($value['validity_time'] == 0) {
                $list[$key]['validity_time'] = intval($value['validity_time']);
            } else {
                $nowstamp = mktime(0,0,0, date('m'), date('d'), date('Y') );
                if ($value['validity_time'] != 0 && $value['validity_time'] < $nowstamp) {
                    unset($list[$key]);
                }
            }
        }
        return $list;
    }

    /**
     *  公众号导入
     * @return \Illuminate\Http\JsonResponse
     */
    public function import()
    {
        set_time_limit(0);
        $this->uniacid = \YunShop::app()->uniacid;
        $this->uniacids = request()->input('uniacids');
        DB::beginTransaction();
        foreach ($this->func as $value) {
            try {
                $this->$value();
            } catch (\Exception $e) {
                DB::rollback();  //回滚
                \Log::debug($e);
                return $this->errorJson('导入失败');
            }
        }
        DB::commit();
        return $this->successJson('ok');
    }

    /**
     * 导入商品+品牌
     */
    private function importGoods()
    {
        $list = DB::table('yz_goods')
            ->whereNull('deleted_at')
            ->where('uniacid', $this->uniacids)
            ->where('yz_goods.is_plugin', 0)
            ->where('yz_goods.plugin_id', 0)
            ->get()
            ->toArray();

        //商品品牌
        $this->oldGoodsListId = array_column($list, 'id');
        $array['brand_list'] = DB::table('yz_brand')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->get();
        foreach ($array['brand_list'] as $key => $value) {
            $values = [
                'uniacid' => $this->uniacid,
                'name' => $value['name'],
                'alias' => $value['alias'],
                'logo' => $value['logo'],
                'desc' => $value['desc'],
                'created_at' => $_SERVER['REQUEST_TIME'],
                'updated_at' => $_SERVER['REQUEST_TIME'],
                'is_recommend' => $value['is_recommend'],
            ];
            $array['brand_lists'][$key] = [
                'new_id' => DB::table('yz_brand')->insertGetId($values),
                'old_id' => $value['id'],
            ];
        }
        unset($array['brand_list']);
        //商品基本信息
        $array['brand_lists'] = array_column($array['brand_lists'], null, 'old_id');
        foreach ($list as $key => $value) {
            $array['goods'][$key] = [
                'uniacid' => $this->uniacid,
                'display_order' => $value['display_order'],
                'title' => $value['title'],
                'brand_id' => $array['brand_lists'][$value['brand_id']]['new_id'] ?: 0,
                'type' => $value['type'],
                'sku' => $value['sku'],
                'status' => $value['status'],
                'description' => $value['description'],
                'is_recommand' => $value['is_recommand'],
                'has_option' => $value['has_option'],
                'show_sales' => $value['show_sales'],
                'thumb_url' => $value['thumb_url'],
                'is_new' => $value['is_new'],
                'is_hot' => $value['is_hot'],
                'is_discount' => $value['is_discount'],
                'thumb' => $value['thumb'],
                'goods_sn' => $value['goods_sn'],
                'product_sn' => $value['product_sn'],
                'price' => $value['price'],
                'market_price' => $value['market_price'],
                'cost_price' => $value['cost_price'],
                'weight' => $value['weight'],
                'stock' => $value['stock'],
                'virtual_sales' => $value['virtual_sales'],
                'reduce_stock_method' => $value['reduce_stock_method'],
                'no_refund' => $value['no_refund'],
                'status' => $value['status'],
                'content' => $value['content'],
                'old_id' => $value['id'],
                'created_at' => $_SERVER['REQUEST_TIME'],
                'updated_at' => $_SERVER['REQUEST_TIME'],
            ];
        }
        DB::table('yz_goods')->insert($array['goods']);

        $array['goods_list'] = DB::table('yz_goods')
            ->select('id', 'old_id')
            ->whereIn('old_id', $this->oldGoodsListId)
            ->get()
            ->toArray();
        DB::table('yz_goods')->where('id','>','0')->update(['old_id'=> 0]);
        $this->goodsListId = array_column($array['goods_list'], null, 'old_id');
    }

    /**
     * 导入商品分类
     */
    private function importGoodsCategory()
    {
        //商品分类，在category表中parent_id如果不为0,那么必须先添加他的上级
        $array['goods_category'] = DB::table('yz_goods_category')
            ->whereIn('goods_id', $this->oldGoodsListId)
            ->get()
            ->toArray();

        $category = DB::table('yz_category')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->where('plugin_id', 0)
            ->orderBy('parent_id')//为了让parent_id先添加，防止添加了不等于0的数据后面找不到
            ->get()
            ->toArray();

        $res = array();
        $i = 0;
        foreach ($category as $k => $v) {
            $v['uniacid'] = $this->uniacid;
            $v['created_at'] = $_SERVER['REQUEST_TIME'];
            $v['updated_at'] = $_SERVER['REQUEST_TIME'];
            if ($v['parent_id'] != 0) {
                foreach ($res as $g) {
                    if ($g['old_category_id'] == $v['parent_id']) {
                        $v['parent_id'] = $g['new_category_id'];
                    }
                }
            }
            $i++;
            $res[$i]['old_category_id'] = $v['id'];
            unset($v['id']);
            $res[$i]['new_category_id'] = DB::table('yz_category')->insertGetId($v);
        }

        $array['category_ids_listss'] = array_column($res, null, 'old_category_id');
        foreach ($array['goods_category'] as $key => $value) {
            $temp = explode(",", $value['category_ids']);
            $categoryIds = '';
            for ($i = 0; $i <= count($temp); $i++) {
                $categoryIds .= $array['category_ids_listss'][$temp[$i]]['new_category_id'] . ',';
            }
            $array['goods_category'][$key] = [
                'goods_id' => $this->goodsListId[$value['goods_id']]['id'],
                'category_id' => $array['category_ids_listss'][$value['category_id']]['new_category_id'],
                'category_ids' => rtrim($categoryIds, ","),
                'created_at' => $_SERVER['REQUEST_TIME'],
                'updated_at' => $_SERVER['REQUEST_TIME'],
            ];
        }
        DB::table('yz_goods_category')->insert($array['goods_category']);
    }

    /**
     * 导入商品营销
     */
    private function importGoodsSale()
    {
        //商品营销
        $array['goods_sale'] = DB::table('yz_goods_sale')
            ->whereIn('goods_id', $this->oldGoodsListId)
            ->get()
            ->toArray();
        foreach ($array['goods_sale'] as $key => $value) {
            $array['goods_sale'][$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'];
            $array['goods_sale'][$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $array['goods_sale'][$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($array['goods_sale'][$key]['id']);
        }
        DB::table('yz_goods_sale')->insert($array['goods_sale']);
    }

    /**
     * 导入商品视频
     */
    private function importGoodsVideo()
    {
        //首页视频
        $array['goods_video'] = DB::table('yz_goods_video')
            ->whereIn('goods_id', $this->oldGoodsListId)
            ->get()
            ->toArray();
        foreach ($array['goods_video'] as $key => $value) {
            unset($array['goods_video'][$key]['id']);
            $array['goods_video'][$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'];
            $array['goods_video'][$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $array['goods_video'][$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
        }
        DB::table('yz_goods_video')->insert($array['goods_video']);
    }

    /**
     * 导入商品折扣
     */
    private function importGoodsDiscount()
    {
        //商品折扣
        $array['goods_discount'] = DB::table('yz_goods_discount')
            ->whereIn('goods_id', $this->oldGoodsListId)
            ->get()
            ->toArray();
        foreach ($array['goods_discount'] as $key => $value) {
            $array['goods_discount'][$key] = [
                'goods_id' => $this->goodsListId[$value['goods_id']]['id'],
                'created_at' => $_SERVER['REQUEST_TIME'],
                'updated_at' => $_SERVER['REQUEST_TIME'],
                'level_discount_type' => 0,
                'discount_method' => 0,
                'level_id' => 0,
                "discount_value" => "",
            ];
        }
        DB::table('yz_goods_discount')->insert($array['goods_discount']);
    }

    /**
     * 导入商品运费，并附初始值
     */
    private function importGoodsDispatch()
    {
        //商品配送运费模板
        $array['goods_dispatch'] = DB::table('yz_goods_dispatch')
            ->whereIn('goods_id', $this->oldGoodsListId)
            ->get()
            ->toArray();
        $array['dispatch_id'] = array_unique(array_column($array['goods_discount'], 'dispatch_id'));
        foreach ($array['goods_dispatch'] as $key => $value) {
            $array['goods_dispatch'][$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'];
            $array['goods_dispatch'][$key]['dispatch_type'] = 1; //统一运费
            $array['goods_dispatch'][$key]['dispatch_price'] = $value['dispatch_price'];
            $array['goods_dispatch'][$key]['is_cod'] = $value['is_cod'];
            $array['goods_dispatch'][$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $array['goods_dispatch'][$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($array['goods_dispatch'][$key]['id']);
        }
        DB::table('yz_goods_dispatch')->insert($array['goods_dispatch']);
    }

    /**
     * 导入商品参数
     */
    private function importGoodsParam()
    {
        $array['goods_param'] = DB::table('yz_goods_param')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        foreach ($array['goods_param'] as $key => $value) {
            $array['goods_param'][$key]['uniacid'] = $this->uniacid;
            $array['goods_param'][$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'];
            $array['goods_param'][$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $array['goods_param'][$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($array['goods_param'][$key]['id']);
        }
        DB::table('yz_goods_param')->insert($array['goods_param']);
    }

    /**
     * 导入商品规格
     */
    private function importGoodsSpec()
    {
        $goodsOption = DB::table('yz_goods_option')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        $goodsSpec = DB::table('yz_goods_spec')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        $goodsSpecItem = DB::table('yz_goods_spec_item')
            ->where('uniacid', $this->uniacids)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();

        $oldSpecOldId = array_column($goodsSpecItem, 'id');

        foreach ($goodsSpecItem as $key => $value) {
            $goodsSpecItem[$key]['uniacid'] = $this->uniacid;
            $goodsSpecItem[$key]['old_id'] = $value['id'];
            $goodsSpecItem[$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $goodsSpecItem[$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($goodsSpecItem[$key]['id']);
        }
        DB::table('yz_goods_spec_item')->insert($goodsSpecItem);

        $goodsSpecItemIdLists = DB::table('yz_goods_spec_item')
            ->select('id', 'old_id', 'specid')
            ->whereIn('old_id', $oldSpecOldId)
            ->where('uniacid', $this->uniacid)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        DB::table('yz_goods_spec_item')->where('id','>','0')->update(['old_id'=> 0]);
        $goodsSpecItemIdList = array_column($goodsSpecItemIdLists, null, 'old_id');

        $goodsSpecListOldId = array_column($goodsSpec, 'id');

        foreach ($goodsSpec as $key => $value) {
            $temps = []; //数组置空
            if ($value['content']) {
                $temp = unserialize($value['content']);
                foreach ($temp as $k => $v) {
                    $temps[$k] = $goodsSpecItemIdList[$v]['id'];
                }
            }
            $goodsSpec[$key]['old_id'] = $value['id'];
            $goodsSpec[$key]['content'] = serialize($temps);
            $goodsSpec[$key]['uniacid'] = $this->uniacid;
            $goodsSpec[$key]['goods_id'] = $array[$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'] ?: 0;
            $goodsSpec[$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $goodsSpec[$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($goodsSpec[$key]['id']);
        }
        DB::table('yz_goods_spec')->insert($goodsSpec);
        $goodsSpecListId = DB::table('yz_goods_spec')
            ->select('id', 'old_id')
            ->whereIn('old_id', $goodsSpecListOldId)
            ->where('uniacid', $this->uniacid)
            ->whereNull('deleted_at')
            ->get()
            ->toArray();
        DB::table('yz_goods_spec')->where('id','>','0')->update(['old_id'=> 0]);
        $goodsSpecListIds = array_column($goodsSpecListId, null, 'old_id');
        $result = array();
        foreach ($goodsSpecItemIdLists as $key => $value) {
            $result[$key] = [
                'id' => $value['id'],
                'specid' => $goodsSpecListIds[$value['specid']]['id'] ?: 0,
            ];
        }
        $this->updateBatch($result);
        foreach ($goodsOption as $key => $value) {

            $specs = '';
            if ($value['specs']) {
                $temps = explode("_", $value['specs']);
                if (count($temps) == 1) {
                    $specs = $goodsSpecItemIdList[$value['specs']]['id'];
                } else {
                    foreach ($temps as $k => $v) {
                        $specs .= $goodsSpecItemIdList[$v]['id'] . '_';
                    }
                    $specs = rtrim($specs, '_');
                }
            }
            $goodsOption[$key]['specs'] = $specs;
            $goodsOption[$key]['uniacid'] = $this->uniacid;
            $goodsOption[$key]['goods_id'] = $array[$key]['goods_id'] = $this->goodsListId[$value['goods_id']]['id'];
            $goodsOption[$key]['created_at'] = $_SERVER['REQUEST_TIME'];
            $goodsOption[$key]['updated_at'] = $_SERVER['REQUEST_TIME'];
            unset($goodsOption[$key]['id']);
        }
        DB::table('yz_goods_option')->insert($goodsOption);
    }

    /**
     *
     * todo 该sql批量更新超过两百条数据需要优化，应该把他用循环拆开
     * 批量更新
     * @param array $multipleData 传入的数组，默认更新主键ID
     * @return bool
     */
    public function updateBatch($multipleData = [])
    {
        if (empty($multipleData)) {
            return false;
        }
        $tableName = DB::getTablePrefix() . 'yz_goods_spec_item'; // 表名
        //获取第一项
        $firstRow = current($multipleData);

        $updateColumn = array_keys($firstRow);
        // 默认以id为条件更新，如果没有ID则以第一个字段为条件
        $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
        unset($updateColumn[0]);
        // 拼接sql语句
        $updateSql = "UPDATE " . $tableName . " SET ";
        $sets = [];
        $bindings = [];
        foreach ($updateColumn as $uColumn) {
            $setSql = "`" . $uColumn . "` = CASE ";
            foreach ($multipleData as $data) {
                $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                $bindings[] = $data[$referenceColumn];
                $bindings[] = $data[$uColumn];
            }
            $setSql .= "ELSE `" . $uColumn . "` END ";
            $sets[] = $setSql;
        }
        $updateSql .= implode(', ', $sets);
        //拆分条件
        $whereIn = collect($multipleData)->pluck($referenceColumn)->values()->all();
        $bindings = array_merge($bindings, $whereIn);
        $whereIn = rtrim(str_repeat('?,', count($whereIn)), ',');
        $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
        // 传入预处理sql语句和对应绑定数据
        return DB::update($updateSql, $bindings);
    }
}