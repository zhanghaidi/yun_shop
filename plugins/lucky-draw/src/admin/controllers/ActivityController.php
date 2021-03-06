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
                return $this->successJson('????????????', [
                    'id' => $activityModel->id,
                ]);
            }
        }

        $love_status = 0;

        $love_name = '?????????';
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
                return $this->successJson('????????????');
            }
        }

        $activityModel = DrawActivityModel::uniacid()->with(['hasOneCoupon', 'hasOneGoods'])->find($id);
        if (empty($activityModel->goods_id)) {
            $activityModel->goods_id = '';
        }
        $prize_list = DrawPrizeModel::uniacid()->whereIn('id', $activityModel->prize_id)->get();

        $love_status = 0;

        $love_name = '?????????';
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
            return $this->successJson('????????????');
        }
    }

    public function record()
    {
        $id = (int)request()->id;

        $activityModel = DrawActivityModel::uniacid()->select('id', 'name', 'countdown_time')->find($id);
        $logs = DrawPrizeRecordModel::builder()->where('activity_id', $id)->orderBy('id', 'desc')->paginate()->toArray();
        
        $love_name = '?????????';
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

        $love_name = '?????????';
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
        $love_name = '?????????';
        if (app('plugins')->isEnabled('love')) {
            $love_name = SetService::getLoveName();
        }
        $file_name = date('Ymdhis', time()) . '??????????????????';

        $search = request()->search;

        $list = DrawPrizeRecordModel::getRecord($search)->whereHas('hasOneActivity', function ($q) use ($search){
            $q->where('id', $search['id']);
        })->orderBy('id', 'desc');

        $export_page = request()->export_page ? request()->export_page : 1;
        $export_model = new ExportService($list, $export_page);

        $export_data[0] = [
            '??????',
            '??????id',
            '?????????',
            '????????????',
            '????????????',
            '????????????',
        ];

        foreach ($export_model->builder_model as $key => $item) {
            $detail = '';
            switch ($item['hasOnePrize']['type']) {
                case 1:
                    $detail = $item['hasOneCoupon']['name'];
                    break;
                case 2:
                    $detail = $item['hasOnePrize']['point'].'??????';
                    break;
                case 3:
                    $detail = $item['hasOnePrize']['love'].$love_name;
                    break;
                case 4:
                    $detail = $item['hasOnePrize']['amount'].'??????';
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
                return $this->successJson('????????????', [
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
                return $this->successJson('????????????');
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
            return $this->successJson('????????????');
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
            return $this->errorJson('?????????????????????.');
        }

        if (!$file->isValid()) {
            return $this->errorJson('????????????.');
        }

        if ($file->getClientSize() > 30*1024*1024) {
            return $this->errorJson('????????????.');
        }

        $defaultImgType = [
            'jpg', 'bmp', 'eps', 'gif', 'mif', 'miff', 'png', 'tif',
            'tiff', 'svg', 'wmf', 'jpe', 'jpeg', 'dib', 'ico', 'tga', 'cut', 'pic'
        ];

        // ????????????????????????
        $originalName = $file->getClientOriginalName(); // ????????????
        $realPath = $file->getRealPath();   //???????????????????????????
        $ext = $file->getClientOriginalExtension(); //????????????

        if (!$ext) {
            $ext = 'jpg';
        }
        $newOriginalName = md5($originalName . str_random(6)) . '.' . $ext;

        if (env('APP_Framework') == 'platform') {
            $setting = SystemSetting::settingLoad('global', 'system_global');

            $remote = SystemSetting::settingLoad('remote', 'system_remote');

            if (in_array($ext, $defaultImgType)) {
                if ($setting['image_extentions'] && !in_array($ext, array_filter($setting['image_extentions'])) ) {
                    return $this->errorJson('??????????????????????????????');
                }
                $defaultImgSize = $setting['img_size'] ? $setting['img_size'] * 1024 : 1024*1024*5; //???????????????5M
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('???????????????????????????');
                }
            }

            if ($setting['image']['zip_percentage']) {
                //??????????????????
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
                    return $this->errorJson('??????????????????????????????');
                }
                $defaultImgSize = $upload['image']['limit'] ? $upload['image']['limit'] * 1024 : 5 * 1024 * 1024;
                if ($file->getClientSize() > $defaultImgSize) {
                    return $this->errorJson('???????????????????????????');
                }
            }

            if ($upload['image']['zip_percentage']) {
                //??????????????????
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
            //????????????
            $result = \Storage::disk('syst_images')->put($newOriginalName, file_get_contents($realPath));
            if (!$result) {
                return $this->errorJson('????????????');
            }
            //????????????
            if ($remote['type'] != 0) {
                file_remote_upload($newOriginalName, true, $remote);
            }

            return $this->successJson('????????????', [
                'thumb' => \Storage::disk('syst_images')->url($newOriginalName),
                'thumb_url' => yz_tomedia(\Storage::disk('syst_images')->url($newOriginalName)),
                'attach' => $attach,
            ]);
        } else {
            //????????????
            $result = \Storage::disk('image')->put($newOriginalName, file_get_contents($realPath));
            if (!$result) {
                return $this->errorJson('????????????');
            }
            //????????????
            if ($remote['type'] != 0) {
                file_remote_upload_wq($newOriginalName, true);
            }

            return $this->successJson('????????????', [
                'thumb' => \Storage::disk('image')->url($newOriginalName),
                'thumb_url' => yz_tomedia(\Storage::disk('image')->url($newOriginalName)),
                'attach' => $attach,
            ]);
        }
    }
}