<?php
/**
 * Created by PhpStorm.
 * User: weifeng
 * Date: 2019-06-11
 * Time: 09:45
 */

namespace Yunshop\LuckyDraw\admin\controllers;


use app\backend\modules\member\models\MemberLevel;
use app\common\components\BaseController;
use app\common\helpers\QrCodeHelper;
use app\common\services\ExportService;
use app\common\services\ImageZip;
use app\platform\modules\system\models\SystemSetting;
use Yunshop\Love\Common\Services\SetService;
use Yunshop\LuckyDraw\admin\models\GoodsModel;
use Yunshop\LuckyDraw\common\models\DrawActivityModel;
use Yunshop\LuckyDraw\common\models\DrawPrizeModel;
use Yunshop\LuckyDraw\admin\models\CouponModel;
use Yunshop\LuckyDraw\common\models\DrawPrizeRecordModel;

class ActivityController extends BaseController
{
    public function index()
    {
        return view('Yunshop\LuckyDraw::admin.list', [

        ])->render();
    }

    public function getList()
    {
        $page_list = DrawActivityModel::uniacid()
            ->with(['hasManyLog' => function ($q) {
                $q->groupBy('activity_id', 'member_id');
            }, 'hasManyRecord' => function ($q) {
                $q->groupBy('activity_id', 'member_id');
            }])
            ->orderBy('created_at', 'desc')
            ->paginate()
            ->toArray();

        $page_list['data'] = collect($page_list['data'])->map(function ($item) {
            $param_arr = [
                'mark' => 'draw_activity',
                'lotteryId' => $item['id'],
            ];

            $prize_list = DrawPrizeModel::uniacid()->select('id', 'name')->whereIn('id', $item['prize_id'])->get();

            $activity_url = yzAppFullUrl(DrawActivityModel::ACTIVITY_QRCODE_URL, $param_arr);
            $code = new QrCodeHelper($activity_url, 'app/public/draw_activity');
            $qr_code = $code->url();

            $item = collect($item)->put('qr_code', $qr_code)
                ->put('activity_url', $activity_url)
                ->put('qr_code', $qr_code)
                ->put('log_count', count($item['has_many_log']))
                ->put('record_count', count($item['has_many_record']))
                ->put('prize_list', $prize_list);

            return $item->toArray();
        });

        $page_list['data'] = $page_list['data']->toArray();

        return $this->successJson('ok', [
            'page_list' => $page_list,
        ]);
    }

    public function search()
    {
        $search_form = request()->search;

        if ($search_form) {
            $page_list = DrawActivityModel::getActivity($search_form)
                ->with(['hasManyLog' => function ($q) {
                    $q->groupBy('activity_id', 'member_id');
                }, 'hasManyRecord' => function ($q) {
                    $q->groupBy('activity_id', 'member_id');
                }])
                ->orderBy('created_at', 'desc')
                ->paginate()->toArray();

            $page_list['data'] = collect($page_list['data'])->map(function ($item) {
                $param_arr = [
                    'mark' => 'draw_activity',
                    'lotteryId' => $item['id'],
                ];

                $prize_list = DrawPrizeModel::uniacid()->select('id', 'name')->whereIn('id', $item['prize_id'])->get();

                $activity_url = yzAppFullUrl(DrawActivityModel::ACTIVITY_QRCODE_URL, $param_arr);
                $code = new QrCodeHelper($activity_url, 'app/public/draw_activity');
                $qr_code = $code->url();

                $item = collect($item)->put('qr_code', $qr_code)
                    ->put('activity_url', $activity_url)
                    ->put('log_count', count($item['has_many_log']))
                    ->put('record_count', count($item['has_many_record']))
                    ->put('qr_code', $qr_code)
                    ->put('prize_list', $prize_list);
                return $item->toArray();
            });

            $pageList['data'] = $page_list['data']->toArray();
            
            return $this->successJson('ok', [
                'page_list' => $page_list,
            ]);
        }
    }

    public function add()
    {
        $form_data = request()->form_data;

        if ($form_data) {
            $activityModel = new DrawActivityModel();
            $activityModel->fill($form_data);
            $activityModel->uniacid = \YunShop::app()->uniacid;
            if ($activityModel->save()) {
                return $this->successJson('添加成功', [
                    'id' => $activityModel->id,
                ]);
            }
        }

        $love_status = 0;

        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
            $love_status = 1;
        }

        return view('Yunshop\LuckyDraw::admin.add', [
            'love_name' => json_encode($love_name),
            'love_status' => json_encode($love_status),
        ])->render();
    }

    public function getActivityById()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::uniacid()->find($id);

        $par_arr = [
            'lotteryId' => $activityModel->id,
            'mark' => 'draw_activity',
        ];

        $activity_url = yzAppFullUrl(DrawActivityModel::ACTIVITY_QRCODE_URL, $par_arr);

        $code = new QrCodeHelper($activity_url, 'app/public/draw_activity');
        $qr_code = $code->url();

        $activityModel = collect($activityModel)->put('activity_url', $activity_url)->put('qrCode', $qr_code);

        return $this->successJson('ok', [
            'activityModel' => $activityModel,
        ]);
    }

    public function editIndex()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::uniacid()->find($id);

        if ($activityModel) {
            return $this->successJson('ok', [
                'activityModel' => $activityModel,
                'id' => $id,
            ]);
        }
    }

    public function edit()
    {
        $id = (int)request()->id;
        $form_data = request()->form_data;

        if ($form_data) {
            $activityModel = DrawActivityModel::uniacid()->find($form_data['id']);
            $activityModel->fill($form_data);
            if ($activityModel->save()) {
                return $this->successJson('编辑成功');
            }
        }

        $activityModel = DrawActivityModel::uniacid()->with(['hasOneCoupon', 'hasOneGoods'])->find($id);
        if (empty($activityModel->goods_id)) {
            $activityModel->goods_id = '';
        }
        $prize_list = DrawPrizeModel::uniacid()->whereIn('id', $activityModel->prize_id)->get();

        $love_status = 0;

        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
            $love_status = 1;
        }

        return view('Yunshop\LuckyDraw::admin.edit', [
            'activityModel' => $activityModel,
            'prize_list' => $prize_list,
            'love_name' => json_encode($love_name),
            'love_status' => json_encode($love_status),
        ])->render();
    }

    public function del()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::find($id);
        if ($activityModel->delete()) {
            return $this->successJson('删除成功');
        }
    }

    public function record()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::uniacid()->select('id', 'name', 'countdown_time')->find($id);
        $logs = DrawPrizeRecordModel::builder()->where('activity_id', $id)->orderBy('id', 'desc')->paginate()->toArray();
        
        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }

        return view('Yunshop\LuckyDraw::admin.record', [
            'activityModel' => json_encode($activityModel),
            'logs' => json_encode($logs),
            'love_name' => json_encode($love_name),
        ])->render();
    }

    public function recordPage()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::uniacid()->select('id', 'name', 'countdown_time')->find($id);
        $logs = DrawPrizeRecordModel::builder()->where('activity_id', $id)->orderBy('id', 'desc')->paginate()->toArray();

        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }

        return $this->successJson('ok', [
            'logs' => $logs,
            'activityModel' => $activityModel,
            'love_name' => json_encode($love_name),
        ]);
    }

    public function searchRecord()
    {
        $id = (int)request()->id;
        $search_form = request()->search;

        if ($search_form) {
            $logs = DrawPrizeRecordModel::getRecord($search_form)->whereHas('hasOneActivity', function ($q) use ($id) {
                $q->where('id', $id);
            })->orderBy('id', 'desc')->paginate()->toArray();

            return $this->successJson('ok', [
                'logs' => $logs,
            ]);
        }
    }

    public function export()
    {
        $love_name = '爱心值';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }
        $file_name = date('Ymdhis', time()) . '抽奖数据导出';

        $search = request()->search;

        $list = DrawPrizeRecordModel::getRecord($search)->whereHas('hasOneActivity', function ($q) use ($search){
            $q->where('id', $search['id']);
        })->orderBy('id', 'desc');

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($list, $export_page);

        $export_data[0] = [
            '昵称',
            '会员id',
            '手机号',
            '抽奖时间',
            '中奖信息',
            '奖品信息',
        ];

        foreach ($export_model->builder_model as $key => $item) {
            $detail = '';
            switch ($item['hasOnePrize']['type']) {
                case 1:
                    $detail = $item['hasOneCoupon']['name'];
                    break;
                case 2:
                    $detail = $item['hasOnePrize']['point'].'积分';
                    break;
                case 3:
                    $detail = $item['hasOnePrize']['love'].$love_name;
                    break;
                case 4:
                    $detail = $item['hasOnePrize']['amount'].'余额';
                    break;
            }
            $export_data[$key + 1] = [
                $item['member']['nickname'],
                $item['member']['uid'],
                $item['member']['mobile'],
                date('Y-m-d H:i:s', $item['created_at']->timestamp),
                $item['hasOnePrize']['name'],
                $detail,
            ];
        }

        $export_model->export($file_name, $export_data, \Request::query('route'));
    }

    public function addPrize()
    {
        $form_data = request()->form_data;

        if ($form_data) {
            $prizeModel = new DrawPrizeModel();
            $prizeModel->fill($form_data);
            $prizeModel->uniacid = \YunShop::app()->uniacid;

            if ($prizeModel->save()) {
                return $this->successJson('添加成功', [
                    'id' => $prizeModel->id,
                ]);
            }
        }
    }

    public function editPrize()
    {
        $id = (int)request()->id;
        $form_data = request()->form_data;

        if ($form_data) {
            $prizeModel = DrawPrizeModel::find($form_data['id']);
            $prizeModel->fill($form_data);

            if ($prizeModel->save()) {
                return $this->successJson('编辑成功');
            }
        }

        $prizeModel = DrawPrizeModel::with(['hasOneCoupon' => function ($q) {
            $q->select(['id', 'name']);
        }])->find($id);

        return $this->successJson('ok', [
            'prizeModel' => $prizeModel,
        ]);
    }

    public function delPrize()
    {
        $id = (int)request()->id;

        $prizeModel = DrawPrizeModel::find($id);
        if ($prizeModel->delete()) {
            return $this->successJson('删除成功');
        }
    }

    public function getPrizeList()
    {
        $prizeList = DrawPrizeModel::uniacid()->get();

        return $this->successJson('ok', [
            'prizeList' => $prizeList,
        ]);
    }

    public function getCoupons()
    {
        $kwd = request()->kwd;
        $coupons = CouponModel::getCouponByKwd($kwd);

        return $this->successJson('ok', [
            'coupons' => $coupons,
        ]);
    }

    public function getMemberLevels()
    {
        $memberLevels = MemberLevel::getMemberLevelList();

        $memberLevels = array_merge($this->getDefaultLevel(), $memberLevels);

        return $this->successJson('ok', [
           'memberLevels' =>  $memberLevels,
        ]);
    }

    public function getDefaultLevel()
    {
        $setting_name = \Setting::get('shop.member.level_name');

        $arr[0] = [
            'id' => 0,
            'uniacid' => \YunShop::app()->uniacid,
            'level' => 0,
            'level_name' => $setting_name,
        ];

        return $arr;
    }

    public function getGoods()
    {
        $kwd = request()->keyword;

        if ($kwd) {
            $goodsModels = GoodsModel::getGoodsByName($kwd)->toArray();
            return $this->successJson('ok', $goodsModels);
        }
    }

    public function upload()
    {
        $attach = request()->attach;
        $file = request()->file('file');

        if (!$file) {
            return $this->errorJson('请传入正确参数.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('上传失败.');
        }

        if ($file->getClientSize() > 30*1024*1024) {
            return $this->errorJson('图片过大.');
        }

        $defaultImgType = [
            'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
            'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
        ];

        // 获取文件相关信息
        $originalName = $file->getClientOriginalName(); // 文件原名
        $realPath = $file->getRealPath();   //临时文件的绝对路径
        $ext = $file->getClientOriginalExtension(); //文件后缀

        if (!$ext) {
            $ext = 'jpg';
        }
        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

        if (env('APP_Framework') == 'platform') {
            $setting = SystemSetting::settingLoad('global', 'system_global');

            $remote = SystemSetting::settingLoad('remote', 'system_remote');

            if (in_array($ext, $defaultImgType)) {
                if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions'])) ) {
                    return $this->errorJson('非规定类型的文件格式');
                }
                $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024*1024*5; //默认大小为5M
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('文件大小超出规定值');
                }
            }

            if ($setting['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $setting['image']['zip_percentage']
                );
            }

            if ($setting['thumb_width'] == 1 && $setting['thumb_width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $setting['thumb_width']
                );
            }
        } else {
            global $_W;
            $remote = $_W['setting']['remote'];
            $upload = $_W['setting']['upload'];

            if (in_array($ext, $defaultImgType)) {
                if ($upload['image']['extentions'] && !in_array($ext, $upload['image']['extentions'])) {
                    return $this->errorJson('非规定类型的文件格式');
                }
                $defaultImgSize = $upload['image']['limit'] ? $upload['image']['limit'] * 1024 : 5 * 1024 * 1024;
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('文件大小超出规定值');
                }
            }

            if ($upload['image']['zip_percentage']) {
                //执行图片压缩
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $upload['image']['zip_percentage']
                );
            }

            if ($upload['image']['thumb'] == 1 && $upload['image']['width']) {
                $imagezip = new ImageZip();
                $imagezip->makeThumb(
                    yz_tomedia($newOriginalName),
                    yz_tomedia($newOriginalName),
                    $upload['image']['width']
                );
            }
        }

        if (env('APP_Framework') == 'platform') {
            //本地上传
            $result = \Storage::disk('syst_images')->put($newOriginalName, file_get_contents($realPath));
            if (!$result) {
                return $this->errorJson('上传失败');
            }
            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload($newOriginalName, true, $remote);
            }

            return $this->successJson('上传成功', [
                'thumb' => \Storage::disk('syst_images')->url($newOriginalName),
                'thumb_url' => yz_tomedia(\Storage::disk('syst_images')->url($newOriginalName)),
                'attach' => $attach,
            ]);
        } else {
            //本地上传
            $result = \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));
            if (!$result) {
                return $this->errorJson('上传失败');
            }
            //远程上传
            if ($remote['type'] != 0) {
                file_remote_upload_wq($newOriginalName, true);
            }

            return $this->successJson('上传成功', [
                'thumb' => \Storage::disk('image')->url($newOriginalName),
                'thumb_url' => yz_tomedia(\Storage::disk('image')->url($newOriginalName)),
                'attach' => $attach,
            ]);
        }
    }
}