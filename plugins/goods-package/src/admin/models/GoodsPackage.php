<?php
/**
 * Author: 芸众商城 www.yunzshop.com
 * Date: 2017/9/22
 * Time: 上午11:09
 */

namespace Yunshop\GoodsPackage\admin\models;

use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\DB;
use Yunshop\GoodsPackage\common\models\Goods;

class GoodsPackage extends \Yunshop\GoodsPackage\common\models\GoodsPackage
{

    const PAGE_SIZE = 15;

    //通过套餐标题搜索套餐
    public static function getGoodsPackagesByName($keyword)
    {
        return static::uniacid()->select('id', 'title', 'thumb')
            ->where('title', 'like', '%' . $keyword . '%')
            //->where('status', 1)
            ->get();
    }

    //将数据还原成页面输入的格式
    public static function transform($data)
    {
        //通过商品id查找商品名称，并拼接,组装到$data['category_goods_names']
        foreach ($data['has_many_category'] as &$category) {
            $arr = array_filter(explode(';', $category['category_goods_ids']));
            $str = '';
            $GoodsList = Goods::getGoodsListByGoodsIds($arr);
            foreach ($GoodsList as $goods) {
                $str .= "[".$goods->id."]".$goods->title . ';';
            }
            $category['category_goods_names'] = $str;
        }
        $data['other_package_ids'] = array_filter(explode(';', $data['other_package_ids']));
        //通过id查询套餐名称
        $tempPackageName = [];
        foreach ($data['other_package_ids'] as $package_id) {
            $package = self::getGoodsPackageById($package_id);
            $tempPackageName[] = $package->title;
        }
        $data['other_package_names'] = $tempPackageName;
        return $data;
    }



    /**
     * 套餐列表的查询
     * @param array $params 查询参数，数组形式传入
     * @return \app\framework\Database\Eloquent\Collection|array
     */
    public static function search($params)
    {
        //將套餐查詢出來，id,title,start_time,end_time,status
        $packageQuery = self::uniacid()->select('id', 'title', 'limit_time_status', 'start_time', 'end_time', 'status')
            ->with(['hasManyCategory' => function ($categoryQuery) {
                $categoryQuery->where('uniacid', '=', \YunShop::app()->uniacid);
            }]);
        // 拼接搜索条件
        if (!empty($params)) {
            if (!empty($params['title'])) {
                $packageQuery->where('title', 'like', '%' . $params['title'] . '%');
            }
            if ($params['status'] != '') {
                $packageQuery->where('status', '=', $params['status']);
            }
        }
        // 获取所有符合条件的套餐及其套餐下的拥有的栏目
        $packages = $packageQuery->paginate(GoodsPackage::PAGE_SIZE);//get();
        $pager = PaginationHelper::show($packages->total(), $packages->currentPage(), $packages->perPage());
        $packages = $packages->toArray();
        $packages = $packages['data'];

        //将商品id获取，將套餐下的所有商品的总价查詢出來
        foreach ($packages as &$package) {
            $goodsListPriceSum = 0.00;
            foreach ($package['has_many_category'] as $category) {
                $arrIds = array_filter(explode(';', $category['category_goods_ids']));
                $goodsListPrice = Goods::getGoodsListPriceSumByGoodsIds($arrIds);
                $goodsListPriceSum = bcadd($goodsListPriceSum, $goodsListPrice, 2);
            }
            $package['price_sum'] = $goodsListPriceSum;
        }
        return ['packages' => $packages, 'pager' => $pager];
    }

    //对前端页面传入的表单进行处理，符合数据库存储格式，如将商品id和商品名称转为json再存储等
    //传入form表单，传出处理后的form表单
    public static function dataProcess($form)
    {
        // 时间存储时间戳
        $form['start_time'] = strtotime($form['limit_time']['start']);
        $form['end_time'] = strtotime($form['limit_time']['end']);
        unset($form['limit_time']);
        // 其他套餐的id要用分号隔开，存字符串
        $form['other_package_ids'] = !empty($form['other_package_ids']) ? implode(';', $form['other_package_ids']) : '';
        unset($form['other_package_names']);//不要存前台传入的商品名称，没意义
        // 分享设置，如果没有填写，则需要默认值
        $form['share_title'] = !empty($form['share_title']) ? $form['share_title'] : $form['title'];
        $form['share_thumb'] = !empty($form['share_thumb']) ? $form['share_thumb'] : $form['thumb'];
        $form['share_desc'] = !empty($form['share_desc']) ? $form['share_desc'] : (\Setting::get('shop.shop.name') ?: '平台自营');
        // 赋值公众号id
        $form['uniacid'] = \YunShop::app()->uniacid;

        // 组装栏目数据
        $categories = [];
        for ($i = 0; $i < count($form['category']['sort']); $i++) {
            $category = [];
            $category['id'] = $form['category']['id'][$i];
            $category['category_sort'] = $form['category']['sort'][$i];
            $category['category_name'] = $form['category']['cate_name'][$i];
            $category['category_goods_ids'] = $form['category']['goods_ids'][$i];
            $category['uniacid'] = \YunShop::app()->uniacid;
            $categories[] = $category;
        }
        unset($form['category']);

        // 组装幻灯片数据
        $carousels = [];
        for ($i = 0; $i < count($form['carousel']['sort']); $i++) {
            $carousel = [];
            $carousel['id'] = $form['carousel']['id'][$i];
            $carousel['carousel_sort'] = $form['carousel']['sort'][$i];
            $carousel['carousel_title'] = $form['carousel']['title'][$i];
            $carousel['carousel_thumb'] = $form['carousel']['thumb'][$i];
            $carousel['carousel_link'] = $form['carousel']['link'][$i];
            $carousel['carousel_open_status'] = $form['carousel']['is_open'][$i];
            $carousel['uniacid'] = \YunShop::app()->uniacid;
            $carousels[] = $carousel;
        }
        unset($form['carousel']);
        // 返回套餐，栏目，幻灯片数据
        return ['package' => $form, 'categories' => $categories, 'carousels' => $carousels];
    }

    // 保存数据到数据库，该方法适用于创建新套餐时或者编辑套餐时的操作
    public function saveData($data)
    {
        DB::beginTransaction();
        if ($this->save()) {
            // 循环保存栏目和幻灯片
            foreach ($data['categories'] as $category) {
                $category->category_package_id = $this->id;
                if (!$category->save()) {
                    DB::rollBack();
                    return ['status' => 0, 'message' => '幻灯片保存失败,请检查输入情况!'];
                }
            }
            foreach ($data['carousels'] as $carousel) {
                $carousel->carousel_package_id = $this->id;
                if (!$carousel->save()) {
                    DB::rollBack();
                    return ['status' => 0, 'message' => '栏目保存失败,请检查输入情况!'];
                }
            }
        } else {
            return ['status' => 0, 'message' => '套餐保存失败,请检查输入情况!'];
        }
        DB::commit();
        return ['status' => 1, 'message' => '套餐保存成功!', 'data' => ''];
    }

    // 对所有要保存数据库的数据进行验证，套餐，栏目，幻灯片
    public function validateData($data)
    {
        // 验证套餐
        $this->fill($data['package']);
        $validate = $this->validator();
        if ($validate->fails()) {
            return ['status' => 0, 'message' => $validate->messages(), 'data' => ''];
        }
        // 创建并验证栏目
        $goodsPackageCategories = [];
        foreach ($data['categories'] as $category) {
            $goodsPackageCategory = null;
            if (empty($category['id'])) {
                $goodsPackageCategory = new GoodsPackageCategory();
            } else {
                $goodsPackageCategory = GoodsPackageCategory::find($category['id']);
            }
            $goodsPackageCategory->fill($category);
            $categoryValidate = $goodsPackageCategory->validator();
            if ($categoryValidate->fails()) {
                return ['status' => 0, 'message' => $categoryValidate->messages(), 'data' => ''];
            }
            $goodsPackageCategories[] = $goodsPackageCategory;
        }
        // 创建并验证幻灯片
        $goodsPackageCarousels = [];
        foreach ($data['carousels'] as $carousel) {
            $goodsPackageCarousel = null;
            if (empty($carousel['id'])) {
                $goodsPackageCarousel = new GoodsPackageCarousel();
            } else {
                $goodsPackageCarousel = GoodsPackageCarousel::find($carousel['id']);
            }

            $goodsPackageCarousel->fill($carousel);
            $carouselValidate = $goodsPackageCarousel->validator();
            if ($carouselValidate->fails()) {
                return ['status' => 0, 'message' => $carouselValidate->messages(), 'data' => ''];
            }
            $goodsPackageCarousels[] = $goodsPackageCarousel;
        }
        return ['status' => 1, 'message' => '验证成功', 'data' => ['package' => $this, 'categories' => $goodsPackageCategories, 'carousels' => $goodsPackageCarousels]];
    }

    /**
     * 創建商品套餐
     * @param array $package 商品套餐所需的數據
     * @return array
     */
    public function createGoodsPackage($form)
    {
        //对form表单进行处理组装数据库需要的数据
        $data = $this->dataProcess($form);
        $result = $this->validateData($data);
        if (!$result['status']) {
            return $result;
        }
        return $this->saveData($result['data']);
    }

    public static function editGoodsPackage($id)
    {
        //查询单个商品套餐，这时候$result['data']是套餐对象
        $package = self::getGoodsPackageById($id);
        if (!empty($package)) {
            //将数据转化成页面需要的数据类型
            $form = self::transform($package->toArray());
            //查询出商品名称和其他套餐名称，并进行组装
            $result = ['status' => 1, 'message' => '', 'data' => $form];
        } else {
            $result = ['status' => 0, 'message' => '查询不到该套餐!', 'data' => ''];
        }
        return $result;
    }

    public static function saveGoodsPackage($package)
    {
        //根据id获取GoodsPackage
        $goodsPackage = self::getGoodsPackageById($package['id']);
        if (!empty($goodsPackage)) {
            // 将前端删除的栏目，幻灯片进行删除
            foreach ($goodsPackage->hasManyCategory as $category) {
                if (!in_array($category->id, $package['category']['id'])) {
                    $category->delete();
                }
            }
            foreach ($goodsPackage->hasManyCarousel as $carousel) {
                if (!in_array($carousel->id, $package['carousel']['id'])) {
                    $carousel->delete();
                }
            }
            // 处理转换数据，
            $data = self::dataProcess($package);
            // 实例化并验证
            $result = $goodsPackage->validateData($data);
            if (!$result['status']) {
                return $result;
            }
            return $goodsPackage->saveData($result['data']);
        } else {
            $result = ['status' => 0, 'message' => '保存失败,没有找到该套餐!', 'data' => ''];
        }
        return $result;
    }

    public static function deleteGoodsPackage($id)
    {
        $package = self::getGoodsPackageById($id);
        if (!empty($package)) {
            // 栏目，幻灯片进行删除,再删除套餐
            DB::beginTransaction();
            foreach ($package->hasManyCategory as $category) {
                if(!$category->delete()){
                    DB::rollBack();
                    return ['status' => 0, 'message' => '栏目删除失败!', 'data' => ''];
                }
            }
            foreach ($package->hasManyCarousel as $carousel) {
                if(!$carousel->delete()){
                    DB::rollBack();
                    return ['status' => 0, 'message' => '幻灯片删除失败!', 'data' => ''];
                }
            }
            if(!$package->delete()){
                DB::rollBack();
                return ['status' => 0, 'message' => '套餐删除失败!', 'data' => ''];
            } else {
                DB::commit();
                return ['status' => 1, 'message' => '删除成功!', 'data' => ''];
            }
        } else {
            return ['status' => 0, 'message' => '没有找到该套餐，删除失败!', 'data' => ''];
        }
    }
}