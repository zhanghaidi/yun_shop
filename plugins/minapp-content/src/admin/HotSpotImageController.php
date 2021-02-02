<?php

namespace Yunshop\MinappContent\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Illuminate\Support\Facades\DB;
use Yunshop\MinappContent\models\HotSpotModel;
use Yunshop\MinappContent\models\HotSpotImageModel;
use Yunshop\MinappContent\services\MinappContentService;

class HotSpotImageController extends BaseController
{
    private $pageSize = 20;
    protected $hotSpotId;

    public function __construct()
    {

        $hotSpotId =  intval(\YunShop::request()->hotSpotId);
        if(!$hotSpotId){
            return $this->message('热区ID参数错误', '', 'danger');
        }

        $this->hotSpotId = $hotSpotId;


    }

    public function index()
    {

        $searchData = \YunShop::request()->search;

        $list = HotSpotImageModel::uniacid()->where('spot_id', $this->hotSpotId);
        if (isset($searchData['datelimit']['start']) && isset($searchData['datelimit']['end']) &&
            strtotime($searchData['datelimit']['start']) !== false && strtotime($searchData['datelimit']['end']) !== false
        ) {
            $list = $list->where('create_time', '>', strtotime($searchData['datelimit']['start']))
                ->where('create_time', '<', strtotime($searchData['datelimit']['end']) + 86400);
        } else {
            $searchData['datelimit']['start'] = date('Y-m-d H:i:s', strtotime('-1 year'));
            $searchData['datelimit']['end'] = date('Y-m-d H:i:s');
        }
        if (isset($searchData['keywords']) && isset(trim($searchData['keywords'])[0])) {
            $searchData['keywords'] = trim($searchData['keywords']);
            $list = $list->where(function ($query) use ($searchData) {
                $query->where('title', 'LIKE', '%' . $searchData['keywords'] . '%');
            });
        }
        $list = $list->orderBy('list_order', 'desc')
            ->orderBy('id', 'asc')
            ->paginate($this->pageSize)->toArray();
        $pager = PaginationHelper::show($list['total'], $list['current_page'], $this->pageSize);

        return view('Yunshop\MinappContent::admin.hot_spot_image.list', [
            'pluginName' => MinappContentService::get('name'),
            'search' => $searchData,
            'data' => $list['data'],
            'pager' => $pager,
            'hotSpotId' => $this->hotSpotId,
        ]);
    }

    public function edit()
    {
        $data = \YunShop::request()->data;
        if ($data) {
            $data = array_filter($data);

            if (isset($data['id'])) {
                $notice = HotSpotImageModel::where([
                    'id' => $data['id'],
                    'uniacid' => \YunShop::app()->uniacid,
                ])->first();
                if (!isset($notice->id)) {
                    return $this->message('ID参数错误', '', 'danger');
                }
            } else {
                $notice = new HotSpotImageModel;
                $notice->uniacid = \YunShop::app()->uniacid;
            }
            $notice->spot_id = $this->hotSpotId;
            $notice->list_order = isset($data['list_order']) ? $data['list_order'] : 0;
            $notice->image = isset($data['image']) ? $data['image'] : '';
            $notice->status = isset($data['status']) ? $data['status'] : 0;
            $notice->jumpurl = isset($data['jumpurl']) ? $data['jumpurl'] : '';
            $notice->appid = isset($data['appid']) ? $data['appid'] : '';
            //$notice->jumptype = isset($data['jumptype']) ? $data['jumptype'] : 0;
            $notice->save();
            if (!isset($notice->id) || $notice->id <= 0) {
                return $this->message('修改失败', '', 'danger');
            }

            return $this->message('修改成功', Url::absoluteWeb('plugin.minapp-content.admin.hot_spot_image.index',['hotSpotId' => $this->hotSpotId]));
        }

        $id = (int) \YunShop::request()->id;
        if ($id > 0) {
            $infoRs = HotSpotImageModel::where([
                'id' => $id,
                'uniacid' => \YunShop::app()->uniacid,
            ])->first();
            if (!isset($infoRs->id)) {
                return $this->message('热区图片不存在或已被删除', '', 'danger');
            }
        }

        $minappRs = DB::table('account_wxapp')->select('key', 'name')->get()->toArray();

        return view('Yunshop\MinappContent::admin.hot_spot_image.edit', [
            'pluginName' => MinappContentService::get('name'),
            'info' => isset($infoRs) ? $infoRs : null,
            'app' => $minappRs,
            'hotSpotId' => $this->hotSpotId,
        ]);
    }

    public function status()
    {
        $id = \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('参数ID错误', '', 'danger');
        }
        $infoRs = HotSpotImageModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->first();
        if (!isset($infoRs->id)) {
            return $this->message('参数ID数据未找到', '', 'danger');
        }
        $message = '';
        if ($infoRs->status == 1) {
            $infoRs->status = 0;
            $message = '隐藏成功';
        } else {
            $infoRs->status = 1;
            $message = '显示成功';
        }
        $infoRs->save();


        return $this->message($message, Url::absoluteWeb('plugin.minapp-content.admin.hot_spot_image.index',['hotSpotId' => $this->hotSpotId]));
    }

    public function delete()
    {
        $id = (int) \YunShop::request()->id;
        if ($id <= 0) {
            return $this->message('ID参数错误', '', 'danger');
        }

        HotSpotImageModel::where([
            'id' => $id,
            'uniacid' => \YunShop::app()->uniacid,
        ])->delete();

        //return $this->message('删除成功');
        return $this->message("删除成功", Url::absoluteWeb('plugin.minapp-content.admin.hot_spot_image.index',['hotSpotId' => $this->hotSpotId]));
    }
}
