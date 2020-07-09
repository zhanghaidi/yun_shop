<?php


namespace Yunshop\Designer\admin;

use app\common\components\BaseController;
use app\common\exceptions\AppException;
use app\common\exceptions\ShopException;
use app\common\helpers\Cache;
use app\common\models\frame\GoodsGroup;
use app\common\models\frame\Rule;
use app\common\models\frame\RuleKeyword;
use app\common\helpers\PaginationHelper;
use app\common\models\Goods;
use app\common\models\MemberCoupon;
use app\backend\modules\coupon\models\Coupon;
use app\framework\Database\Eloquent\Collection;
use app\frontend\modules\orderGoods\models\PreGeneratedOrderGoodsGroup;
use Yunshop\Designer\Backend\Modules\MemberPage\Controllers\StoreController;
use Yunshop\Designer\Backend\Models\TopMenuModel;
use Yunshop\Designer\Common\Models\MenuModel;
use Yunshop\Designer\Common\Services\MemberDefaultService;
use Yunshop\Designer\models\Designer;
use Yunshop\Designer\models\DesignerMenu;
use Yunshop\Designer\models\MemberDesigner;
use Yunshop\Designer\services\DesignerService;
use Yunshop\Designer\models\GoodsGroupGoods;
use Yunshop\Designer\services\GoodsGroupGoodsService;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\Love\Common\Models\GoodsLove;

class MemberListController extends BaseController
{
    const PAGE_SIZE = 20;

    private $_designer_model;

    //分页列表 + 搜索 todo 可以删除  重构余Yunshop\Designer\Backend\Modules\Page\ControllersRecordsController.index
    public function index()
    {
        $designerList = MemberDesigner::getPageList(static::PAGE_SIZE);
        if (\YunShop::request()->keyword) {
            $designerList = MemberDesigner::getPageListByName(\YunShop::request()->keyword, static::PAGE_SIZE);
        }
        $pager = PaginationHelper::show($designerList->total(), $designerList->currentPage(), $designerList->perPage());

        return view('Yunshop\Designer::admin.list', [
            'designerList' => $designerList,
            'pager' => $pager,
            'keyword' => \YunShop::request()->keyword
        ])->render();
    }

    //预览
    public function preview()
    {

        $page = MemberDesigner::getDesignerByPageID(\YunShop::request()->page_id);
        //echo '<pre>'; print_r($page); exit;
        if (!$page) {
            return $this->message('未获取到店铺装修数据');
        }
        $result = (new DesignerService())->getPage($page->toArray());


        //处理爱心值自定义名称
        if (app('plugins')->isEnabled('love')) {
            $love_basics_set = SetService::getLoveSet();//获取爱心值基础设置
            foreach ($result['data'] as $key => $data) {
                if (in_array($data['temp'], ['goods', 'flashsale', 'memberasset'])) {
                    foreach ($data['data'] as $keys => $loove_name) {
                        foreach ($loove_name as $love_key => $love) {
                            if ($love['name'] == 'love') {
                                $result['data'][$key]['data']['part'][$love_key]['title'] = $love_basics_set['name'] ?: '爱心值';
                            }
                        }
                    }
                }
            }
        }

        //echo '<pre>'; print_r($result); exit;
        //@todo 未完成
        return view('Yunshop\Designer::admin.preview', $result)->render();
    }

    //选择商品 搜索商品
    public function searchGoods()
    {
        $data = file_get_contents("php://input");
        $objData = json_decode($data);
        $goodsList = Goods::getGoodsByNames($objData->keyword)->toArray();
        $love = app('plugins')->isEnabled('love') ? 1 : 0;
        if ($love) {
            $love_set = SetService::getLoveSet();//获取爱心值基础设置
            foreach ($goodsList as $key => $value) {
                $goodsIdList = $this->getLoveGoods($value['id']);//获取单个商品爱心值
                if ($love_set['award'] == 1 && $goodsIdList['award'] == 1 && $goodsIdList['award_proportion'] == 0) {
                    $goodsList[$key]['award_proportion'] = $love_set['award_proportion'];//爱心值比例
                } else {
                    $goodsList[$key]['award_proportion'] = $goodsIdList['award_proportion'];
                }
                $goodsList[$key]['award'] = $goodsIdList['award'];
                $goodsList[$key]['love_name'] = $love_set['name'];
            }
        }
        foreach ($goodsList as &$good) {
            //前端销量 = 真实销量 + 虚拟销量
            $good['real_sales'] += $good['virtual_sales'];

        }
        echo json_encode(set_medias($goodsList, 'thumb'));
        exit;
    }

    //选择限时购商品
    public function searchFlashsale()
    {
        $data = file_get_contents("php://input");
        $objData = json_decode($data);
        $goodsList = Goods::getGoodsByNameForLimitBuy($objData->keyword)->toArray();

        echo json_encode(set_medias($goodsList, 'thumb'));
        exit;
    }

    //搜索文章
    public function searchArticle()
    {
        $data = file_get_contents("php://input");
        $objData = json_decode($data);

        $member_id = \YunShop::app()->getMemberId();

        $articleModel = \Yunshop\Article\models\Article::uniacid()->where('title', 'like', '%' . $objData->keyword . '%')->with('belongsToCategory');

        if (app('plugins')->isEnabled('article-pay')) {
            $articleModel->with(["hasOneArticlePay"=>function ($query) {
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $articleModel->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        $articleList = $articleModel->get()->toArray();
        echo json_encode(set_medias($articleList, 'thumb'));
        exit;
    }

    //搜索优惠券
    public function searchCoupon()
    {
        $data = file_get_contents("php://input");
        $objData = json_decode($data);
        $couponList = Coupon::uniacid()
            ->select(['id', 'display_order', 'name', 'enough', 'coupon_method', 'deduct', 'discount', 'get_type', 'created_at', 'status', 'money', 'total'])
            ->where('name', 'like', '%' . $objData->keyword . '%')->get()->toArray();
        foreach ($couponList as &$item) {
            $item['gettotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->count();
            $item['usetotal'] = MemberCoupon::uniacid()->where("coupon_id", $item['id'])->where("used", 1)->count();
            if ($item['total'] == -1) {
                $item['lasttotal'] = '无限数量';
            } else {
                $lasttotal = $item['total'] - $item['gettotal'];
                $item['lasttotal'] = ($lasttotal > 0) ? $lasttotal : 0; //考虑到可领取总数修改成比之前的设置小, 则会变成负数
            }
            $item['deduct'] = floatval($item['deduct']);
            $item['discount'] = floatval($item['discount']);
            $item['enough'] = floatval($item['enough']);
        }
        echo json_encode(set_medias($couponList, 'thumb'));
        exit;
    }

    //修改默认  页面默认
    public function setToDefault()
    {
        $requestDesigner = MemberDesigner::getDesignerByPageID(\YunShop::request()->id);
        if (!$requestDesigner) {
            die('参数错误！');
        }
        if (\YunShop::request()->d) {
            $oldDefault = MemberDesigner::getDefaultDesigner();
            switch (\YunShop::request()->d) {
                case 'on':
                    $requestDesigner->is_default = 1;

                    MemberDesigner::removeDefault(\YunShop::request()->type);
                    break;
                case 'off':
                    $requestDesigner->is_default = 0;
                    break;
            }
            if ($requestDesigner->update()) {
                Cache::flush();

                $json = array(
                    'result' => 'on',
                    'closeid' => empty($oldDefault) ? '' : $oldDefault->id,
                    'id' => \YunShop::request()->id
                );
                //@todo 应该增加日志记录
                echo json_encode($json);
                exit;
            } else {
                echo json_encode($json = array(
                    'result' => 'off',
                    'id' => \YunShop::request()->id
                ));
            }
        }
    }

    public function setMemberDefault()
    {
        $op = request()->op;
        $designerId = request()->id;
        if (!$designerId) {
            throw new ShopException('id is error!');
        }
        $menuModel = MemberDesigner::find($designerId);

        if (!$menuModel) {
            throw new ShopException('记录错误，请刷新重试！');
        }
        if ($op == 'on') {
            $default = 1;
        } else {
            $default = 0;
        }
        $menuModel->is_default = $default;

        if ($menuModel->save()) {
            return $this->successJson();
        } else {
            return $this->errorJson('操作失败，请刷新重试');
        }
    }

    //检索关键字是否可用
    public function retrievalKeyword()
    {
        $keyWord = \YunShop::request()->keyword;
        $pageId = \YunShop::request()->page_id;

        $result = RuleKeyword::hasKeyword($keyWord);
        if (!$result) {
            echo 'ok';
            exit;
        } else {
            $result = Rule::getRuleByName('yun_shop:designer:' . $result['id']);
            if ($result['name'] == 'yun_shop:designer:' . $pageId) {
                echo 'ok';
                exit;
            }
        }
        echo 'no';
        exit;
    }

    //添加装修页面
    public function store()
    {
        $designerModel = new MemberDesigner();
        $group_goods = new GoodsGroupGoodsService();
        $type = \Yunshop::request()->page_type;
        $designerModel->page_type = $type;
        if (\Yunshop::request()->page_name && \YunShop::request()->page_info) {
            $designer = array(
                'uniacid' => \Yunshop::app()->uniacid,
                'page_name' => \YunShop::request()->page_name,
                'page_type' => \YunShop::request()->page_type,
                'page_info' => htmlspecialchars_decode(\YunShop::request()->page_info),
                'keyword' => $this->getKeyWord(\YunShop::request()->page_info),
                'shop_page_type' => '1',
                'datas' => json_encode($this->imgUrl(\YunShop::request()->datas)),
                'is_default' => \YunShop::request()->is_default,
            );

            // 匹配富文本替换视频标签样式
            $datas = json_decode($designer['datas']);

            foreach ($datas as $key => $item) {
                if ($item->temp == 'richtext') {
                    $item->content = str_replace('width%3D%22300%22%20height%3D%22200', 'width%3D%22100%25%22%20height%3D%22200', $item->content);
                }
            }
            $designer['datas'] = json_encode($datas);

//            $group_goods->FunGroupGoods(\YunShop::request()->datas,\Yunshop::request()->page_type,'insert');
            $designerModel->fill($designer);
            $validator = $designerModel->validator($designerModel->getAttributes());
            if ($validator->fails()) {
                echo json_encode($this->error($validator->messages()));
                exit;
            } else {
                if ($designerModel->save()) {
                    $group_goods->FunGroupGoods(\YunShop::request()->datas, \Yunshop::request()->page_type, 'insert', $designerModel->id);
                    echo $designerModel->id;
                    exit;
                }
            }

        }
        $defaultmenuid = '';
        $pageinfo1 = "{id:'M0000000000000',temp:'topbar',params:{title:'',desc:'',img:'',kw:'',footer:'1',footermenu:'{$defaultmenuid}', floatico:'0',floatstyle:'right',floatwidth:'40px',floattop:'100px',floatimg:'',floatlink:'',top_menu:'0',top_menu_id:''}}";
        $pageinfo = (new MemberDefaultService())->index();
        $love = app('plugins')->isEnabled('love');
        $love_set = '';
        if ($love) {
            $love_set = SetService::getLoveSet();
        }

        //赶进度很多店铺装修的废参数直接移植过来，后期优化
        return view('Yunshop\Designer::memberPage.store', [
            'data' => $this->getPageInfo($pageinfo),
            'pageinfo' => $pageinfo1,
            'designerModel' => $designerModel,
            'menuList' => $this->bottomMenuList(),
            'type' => $type,
            'topMenuList' => $this->topMenuList(),
            'love' => $love,
            'love_set' => $love_set,
            'isEnabledTbk' => app('plugins')->isEnabled('tbk'),
            'signName' => app('plugins')->isEnabled('sign') ? trans('Yunshop\Sign::sign.plugin_name') : '签到'
        ])->render();
    }


    /**
     * @return Collection
     */
    private function topMenuList()
    {
        return TopMenuModel::get();
    }

    /**
     * @return Collection
     */
    private function bottomMenuList()
    {
        $menuModels = MenuModel::uniacid();

        $pageType = \Yunshop::request()->page_type;
        if ($pageType == 9) {
            $menuModels->where('ingress', MenuModel::INGRESS_WE_CHAT_APPLET);
        } else {
            $menuModels->where('ingress', '');
        }
        return $menuModels->get();
    }

    //修改装修页面
    public function update()
    {
        ini_set('memory_limit', -1);
        $group_goods = new GoodsGroupGoodsService();
        $type = \Yunshop::request()->page_type;
        $designerModel = MemberDesigner::getDesignerByPageID(\Yunshop::request()->id);
        if (!$designerModel) {
            echo json_encode("未找到数据或已删除，请刷新重试！");
            exit;
        }
        $datas = json_decode(htmlspecialchars_decode(\YunShop::request()->datas), true);
        $love = app('plugins')->isEnabled('love');

        if ($love) {
            foreach ($datas as $data) {
                $data_arr[] = $data['data'];
                if (isset($data['params']['love'])) {
                    $params_arr = $data['params']['love'];///获取爱心值设置
                }
            }
            foreach ($data_arr as $goodsList) {
                foreach ($goodsList as $goodid) {
                    if (!empty($goodid['goodid']) && isset($goodid['goodid'])) {
                        $goodsid_list['id'] = $goodid['goodid'];//获取商品ID
                        $goodsid_list['award_proportion'] = $goodid['award_proportion'];
                    }
                }
            }
            $love_set = SetService::getLoveSet();
        }
        if (\Yunshop::request()->page_name && \YunShop::request()->page_info) {
            $group_goods->FunGroupGoods(\YunShop::request()->datas, \YunShop::request()->page_type, 'update', \Yunshop::request()->id);
            $designer = array(
                'id' => \YunShop::request()->id,
                'uniacid' => \Yunshop::app()->uniacid,
                'page_name' => \YunShop::request()->page_name,
                'page_type' => \YunShop::request()->page_type,
                'page_info' => \YunShop::request()->page_info,
                'is_default' => \YunShop::request()->is_default,
                'keyword' => $this->getKeyWord(\YunShop::request()->page_info),
                'datas' => json_encode($this->imgUrl(\YunShop::request()->datas)),
            );

            // 替换富文本视频标签样式
            $datas = json_decode($designer['datas']);

            foreach ($datas as $key => $item) {
                if ($item->temp == 'richtext') {
                    $item->content = str_replace('width%3D%22300%22%20height%3D%22200', 'width%3D%22100%25%22%20height%3D%22200', $item->content);
                }
            }

            $designer['datas'] = json_encode($datas);;

            //将关键字附值给挂件，服务观察者
            $designerModel->widgets = $designerModel->keyword;
            $designerModel->fill($designer);
            $validator = $designerModel->validator($designerModel->getAttributes());
            if ($validator->fails()) {
                echo json_encode($this->error($validator->messages()));
                exit;
            } else {
                //todo 关键字修改
                if ($designerModel->save()) {
                    Cache::flush();

                    echo $designerModel->id;
                    exit;
                }
                echo json_encode('数据写入失败');
                exit;
            }
        }
        $page = (new DesignerService())->getPage($designerModel->toArray());
        if ($love) {
            $love_basics_set = SetService::getLoveSet();//获取爱心值基础设置
            foreach ($page['data'] as $key => $data) {
                if ($data['temp'] == 'membercenter')
                {
                    if (app('plugins')->isEnabled('commission')) {
                        $page['data'][$key]['params']['judgecommission'] = true;
                    }
                    if (app('plugins')->isEnabled('team-dividend')) {
                        $page['data'][$key]['params']['judgeteamdividend'] = true;
                    }
                    if(!$data['params']['memberleveltype'])
                    {
                        $page['data'][$key]['params']['memberleveltype'] = 1;
                    }
                }
                if ($data['temp'] == 'nearbygoods')
                {
                    if(!$data['params']['distance'])
                    {
                        $page['data'][$key]['params']['distance'] = 0;
                    }
                }
                if ($data['temp'] == 'memberasset') {
                    foreach ($data['data']['part'] as $lk => $lv) {
                        if ($lv['name'] == 'love') {
                            $page['data'][$key]['data']['part'][$lk]['title'] = $love_basics_set['name'] ?: '爱心值';
                        }
                    }
                }
                if (in_array($data['temp'], ['goods', 'flashsale'])) {
                    foreach ($data['data'] as $keys => $goodsId) {
                        $goodsIdList = $this->getLoveGoods($goodsId['goodid']);//获取单个商品爱心值
                        if (isset($goodsId['goodid'])) { //初始化award
                            if (isset($data['params']['love'])) {
                                $page['data'][$key]['data'][$keys]['award'] = $goodsIdList['award'];
                            } else {
                                $page['data'][$key]['data'][$keys]['award'] = $goodsIdList['award'];
                            }
                            $page['data'][$key]['data'][$keys]['love_name'] = $love_basics_set['name'];
                        }

                        if ($goodsIdList['award_proportion'] == 0) {
                            $page['data'][$key]['data'][$keys]['award_proportion'] = $love_basics_set['award_proportion'];//爱心值比例
                        } else {
                            $page['data'][$key]['data'][$keys]['award_proportion'] = $goodsIdList['award_proportion'];
                        }
                    }
                }
            }
        }

        return view('Yunshop\Designer::memberPage.store', [
            'pageinfo' => $this->getPageInfo(json_encode($page['pageinfo'])),
            'data' => $this->getPageInfo(json_encode($page['data'])),
            'designerModel' => $designerModel,
            'menuList' => $this->bottomMenuList(),
            'type' => $type,
            'topMenuList' => $this->topMenuList(),
            'love' => $love,
            'love_set' => $love_set,
            'isEnabledTbk' => app('plugins')->isEnabled('tbk'),
            'signName' => app('plugins')->isEnabled('sign') ? trans('Yunshop\Sign::sign.plugin_name') : '签到'
        ])->render();
    }

    //删除装修页面
    public function destory()
    {
        $group_goods = new GoodsGroupGoodsService();
        $requestDesigner = MemberDesigner::getDesignerByPageID(\YunShop::request()->page_id);
//        $datas = json_decode(htmlspecialchars_decode($requestDesigner['attributes']['datas']), true);

        if (!$requestDesigner) {
            echo '未找到数据，或已删除！';
            exit;
        } else {
            $group_goods->FunGroupGoods($requestDesigner['attributes']['datas'], $requestDesigner->type, 'delete');//删除商品组商品
            $result = MemberDesigner::destoryDesignerByPageId(\YunShop::request()->page_id);
            if ($result) {
                //@todo 需要监听删除成功同时删除关键子数据表中的关键字数据
                echo 'success';
                exit;
            }
        }
    }


    //处理页面信息值，修改使用
    private function getPageInfo($pageInfo)
    {
        $pageInfo = rtrim($pageInfo, "]");
        $pageInfo = ltrim($pageInfo, "[");

        return $pageInfo;
    }

    //分离关键字 key_word 值 添加使用
    private function getKeyWord($datas)
    {
        $data = htmlspecialchars_decode($datas);
        $data = json_decode($data, true);
        return empty($data[0]['params']['kw']) ? NULL : $data[0]['params']['kw'];
    }

    //处理图片路径
    private function imgUrl($datas)
    {
        $datas = json_decode(htmlspecialchars_decode($datas), true);
        if (is_array($datas)) {
            foreach ($datas as &$data) {
                if ($data['temp'] == 'banner' || $data['temp'] == 'menu' || $data['temp'] == 'picture') {
                    foreach ($data['data'] as &$d) {
                        $d['imgurl'] = yz_tomedia($d['imgurl']);
                    }
                    unset($d);
                } else if ($data['temp'] == 'shop') {
                    $data['params']['bgimg'] = yz_tomedia($data['params']['bgimg']);
                } else if ($data['temp'] == 'goods') {
                    foreach ($data['data'] as &$d) {
                        $d['img'] = yz_tomedia($d['img']);
                    }
                    unset($d);
                } else if ($data['temp'] == 'richtext') {
                    $content = html_images($data['content']);
                    $data['content'] = $content;
                } else if ($data['temp'] == 'cube') {
                    foreach ($data['params']['layout'] as &$row) {
                        foreach ($row as &$col) {
                            $col['imgurl'] = yz_tomedia($col['imgurl']);
                        }
                        unset($col);
                    }
                    unset($row);
                }
            }
        }
        return empty($datas) ? [] : $datas;
    }

    public function selectCategoryGoods()
    {

        $data = file_get_contents("php://input");
        $objData = json_decode($data);
        $goodsList = Goods::Search(['category' => $objData->category_id])->where("status", 1)->take($objData->num)->get()->toArray();
        //判断门店和虚拟插件商品
        foreach ($goodsList as $key => $item) {
            if ($item['plugin_id'] == 31 || $item['plugin_id'] == 60 || $item['plugin_id'] == 20) {
                unset($goodsList[$key]);
            }
        }

        $love = app('plugins')->isEnabled('love') ? 1 : 0;
        if ($love) {
            $love_set = SetService::getLoveSet();//获取爱心值基础设置
            // $love_set['award'];//1是开启   0是关闭
            foreach ($goodsList as $key => $value) {
                $goodsIdList = $this->getLoveGoods($value['id']);
                if ($love_set['award'] == 1 && $goodsIdList['award'] == 1 && $goodsIdList['award_proportion'] == 0) {
                    $goodsList[$key]['award_proportion'] = $love_set['award_proportion'];//爱心值比例
                } else {
                    $goodsList[$key]['award_proportion'] = $goodsIdList['award_proportion'];
                }
                $goodsList[$key]['award'] = $goodsIdList['award'];
                $goodsList[$key]['love_name'] = $love_set['name'];
            }
        }
//        foreach($goodsList as $key=>$value){
//            $goodsIdList=$this->getLoveGoods($value['id']);
//            $goodsList[$key]['award_proportion']= $goodsIdList['award_proportion'];
//            $goodsList[$key]['award']= $goodsIdList['award'];
//        }

        echo json_encode(set_medias($goodsList, 'thumb'));
        exit;
    }

    public function selectSearchGoods()
    {

        $data = file_get_contents("php://input");
        $objData = json_decode($data);
        $goodsList = Goods::Search(['filtering' => $objData->filtering_id])->where("status", 1)->take($objData->num)->get()->toArray();
        //判断门店和虚拟插件商品
        foreach ($goodsList as $key => $item) {
            if ($item['plugin_id'] == 31 || $item['plugin_id'] == 60 || $item['plugin_id'] == 20) {
                unset($goodsList[$key]);
            }
        }
        $love = app('plugins')->isEnabled('love') ? 1 : 0;
        if ($love) {
            $love_set = SetService::getLoveSet();//获取爱心值基础设置
//        $love_set['award'];//1是开启   0是关闭
            foreach ($goodsList as $key => $value) {
                $goodsIdList = $this->getLoveGoods($value['id']);
                if ($love_set['award'] == 1 && $goodsIdList['award'] == 1 && $goodsIdList['award_proportion'] == 0) {
                    $goodsList[$key]['award_proportion'] = $love_set['award_proportion'];//爱心值比例
                } else {
                    $goodsList[$key]['award_proportion'] = $goodsIdList['award_proportion'];
                }
                $goodsList[$key]['award'] = $goodsIdList['award'];
                $goodsList[$key]['love_name'] = $love_set['name'];
            }
        }
        echo json_encode(set_medias($goodsList, 'thumb'));
        exit;
    }

    public function getLoveGoods($goods_id)
    {
        $goodsModel = GoodsLove::select('*')->where('uniacid', \Yunshop::app()->uniacid)->where('goods_id', $goods_id)->first();
        $goods = $goodsModel ? $goodsModel->toArray() : $this->getDefaultGoodsData();
        return $goods;

    }

    private function getDefaultGoodsData()
    {
        return [
            'award' => '0',
            'parent_award' => '0',
            'deduction' => '0',
            'award_proportion' => '0',
            'parent_award_proportion' => '0',
            'second_award_proportion' => '0',
            'deduction_proportion' => '0',
            'deduction_proportion_low' => '0',
            'commission' => [
                'rule' => [

                ],
            ],
        ];
    }

    public function updataGoodsLove($goodsid_list, $award_proportion, $params_arr)
    {
        $update_love = GoodsLove::where('uniacid', \Yunshop::app()->uniacid)
            ->where('goods_id', $goodsid_list)
            ->update(['award' => $params_arr, 'award_proportion' => $award_proportion]);
        return $update_love;

    }

}
