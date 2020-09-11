<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-12-18
 * Time: 16:00
 *
 *    .--,       .--,
 *   ( (  \.---./  ) )
 *    '.__/o   o\__.'
 *       {=  ^  =}
 *        >  -  <
 *       /       \
 *      //       \\
 *     //|   .   |\\
 *     "'\       /'"_.-~^`'-.
 *        \  _  /--'         `
 *      ___)( )(___
 *     (((__) (__)))     梦之所想,心之所向.
 */

namespace Yunshop\Appletslive\admin\controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use app\common\components\BaseController;
use app\common\helpers\Url;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\Appletslive\common\models\Goods;
use app\common\helpers\PaginationHelper;
use Illuminate\Support\Facades\Log;

class GoodsController extends BaseController
{
    // 商品列表
    public function index()
    {
        return view('Yunshop\Appletslive::admin.goods_index', [
        ])->render();
    }

    // 商品编辑
    public function edit()
    {
        if (request()->isMethod('post')) {
            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
        }

        $id = request()->get('id', 0);
        $info = Goods::where('id', $id)->first();

        if (!$info) {
            return $this->message('无效的商品ID', Url::absoluteWeb(''), 'danger');
        }

        return view('Yunshop\Appletslive::admin.room_edit', [
            'id' => $id,
            'info' => $info,
        ])->render();
    }

    // 添加商品
    public function add()
    {
        if (request()->isMethod('post')) {
            return $this->message('保存成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
        }

        return view('Yunshop\Appletslive::admin.goods_add')->render();
    }

    // 删除商品
    public function del()
    {
        return $this->message('删除成功', Url::absoluteWeb('plugin.appletslive.admin.controllers.goods.index'));
    }
}
