<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\MeridianModel;
use function GuzzleHttp\Promise\is_settled;

//穴位|经络控制器
class AcupointController extends ApiController
{
    protected $user_id = 0;
    protected $uniacid = 0;

    /**
     *  constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->uniacid = \YunShop::app()->uniacid;
        $this->user_id = \YunShop::app()->getMemberId();
    }

    /**
     * 经络列表接口
     * $parmares type_id 1：十二经络接口 2：奇经八脉
     */
    public function getMeridian()
    {
        $input_data = request()->all(); //获取经络接口
        $type_id = intval($input_data['type_id']);
        if (empty($type_id)) {
            return $this->errorJson('type_id未发现');
        }
        $cache_key = 'meridian' . $this->uniacid . $type_id;
        $meridian = Cache::get($cache_key);
        if ($meridian->count() <= 0) {
            $meridian = MeridianModel::where(['type_id' => $type_id, 'status' => 1, 'uniacid' => $this->uniacid])
                ->select('id', 'name', 'discription', 'content', 'video', 'audio', 'image', 'start_time', 'end_time', 'recommend_course')
                ->orderBy('list_order', 'DESC')
                ->get();
            if ($meridian->count() > 0) {
                foreach ($meridian as &$v) {
                    if ($v['start_time'] == "00:00:00") {
                        $v['start_time'] = null;
                    } else {
                        $v['start_time'] = substr($v['start_time'], 0, 5);
                    }
                    if ($v['end_time'] == "00:00:00") {
                        $v['end_time'] = null;
                    } else {
                        $v['end_time'] = substr($v['end_time'], 0, 5);
                    }
                    //查询经络穴位
                    $v['acupoint'] = AcupointMerModel::where(['meridian_id' => $v['id'], 'uniacid' => $this->uniacid])->orderBy('sort', 'DESC')->select('acupoint_id AS id', 'acupoint_name AS name')->get();
                    //查询推荐商品
                    if ($v['recommend_course']) {
                        $recommend_course = explode(',', $v['recommend_course']); //关联课程
                        foreach ($recommend_course as $k => $value) {
                            $recommend_course = DB::table('yz_appletslive_replay')->where(['id' => $value, 'uniacid' => $this->uniacid])->select('id', 'rid', 'title', 'cover_img', 'publish_time', 'create_time')->first();
                            $course[$k] = $recommend_course;
                        }
                        $v['recommend_course'] = $course;
                    }
                }
            }
            unset($v);
            //写入缓存
            Cache::forget($cache_key);
            Cache::add($cache_key, $meridian, 60);
        }

        return $this->successJson('请求成功', $meridian);
    }

    /**
     * 将数组按字母A-Z排序 按字母A-Z排序获取穴位
     * @return [type] [description]
     */
    public function getSortAcupoint()
    {
        $cache_key = 'sortAcupoint' . $this->uniacid;
        $data = Cache::get($cache_key);
        if (empty($data)) {
            $acupotion = AcupointModel::where(['uniacid' => $this->uniacid])->select('id', 'name', 'chart')->get();
            $data = [];
            foreach ($acupotion as $k => $v) { //对穴位做排序处理
                $data[$v['chart']]['data'][] = $v;
            }
            ksort($data);
            Cache::forget($cache_key);
            Cache::add($cache_key, $data, 60);
        }

        return $this->successJson('请求成功', $data);
    }
}
