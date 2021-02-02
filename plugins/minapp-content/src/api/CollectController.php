<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;
use \app\common\models\Goods;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Yunshop\Appletslive\common\services\BaseService;
use Yunshop\MinappContent\models\AcupointMerModel;
use Yunshop\MinappContent\models\AcupointModel;
use Yunshop\MinappContent\models\ArticleModel;
use Yunshop\MinappContent\models\MeridianModel;
use app\backend\modules\tracking\models\DiagnosticServiceUser;

//收藏|历史足迹控制器-wk 20210106
class CollectController extends ApiController
{
    protected $ignoreAction = [];

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
     * 用户收藏 token、to_type_id、status  1:穴位收藏 2：疾病收藏 3:文章收藏
     * @return mixed
     */
    public function collect()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;

        $info_id = intval(request()->get('id', 0)); //收藏对象id
        $to_type_id = intval(request()->get('to_type_id', 0)); // 收藏类型 1：穴位 2：病例 3：文章
        if (!$info_id || !$to_type_id) {
            return $this->errorJson('没发现收藏对象信息');
        }
        $is_collect = pdo_get('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => $to_type_id, 'info_id' => $info_id));
        if ($is_collect) {
            $res = pdo_delete('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => $to_type_id, 'info_id' => $info_id));
            if ($res) {
                return $this->errorJson('取消收藏成功', array('status' => 1));
            } else {
                return $this->errorJson('取消收藏失败', array('status' => 2));
            }
        } else {
            //收藏数据
            $collectData = array(
                'user_id' => $user_id,
                'uniacid' => $uniacid,
                'info_id' => $info_id,
                'to_type_id' => $to_type_id,
                'add_time' => time()
            );
            if ($to_type_id == 1) {
                //穴位收藏
                $acupoint = pdo_get('diagnostic_service_acupoint', array('id' => $info_id));
                $collectData['title'] = $acupoint['name'];
                $collectData['description'] = $acupoint['get_position'];
                $collectData['image'] = $acupoint['image'];
            } elseif ($to_type_id == 2) {
                //病例收藏
                $disease = pdo_get('diagnostic_service_disease', array('id' => $info_id));
                $collectData['title'] = $disease['name'];
                $collectData['description'] = $disease['description'];
                $collectData['image'] = $disease['image'];
            } elseif ($to_type_id == 3) {
                //文章收藏
                $article = pdo_get('diagnostic_service_article', array('id' => $info_id));
                //$article['thumb'] = explode(',',$article['thumb']);
                $collectData['title'] = $article['title'];
                $collectData['description'] = $article['description'];
                $collectData['image'] = $article['thumb'];
            }
            $res = pdo_insert('diagnostic_service_collect', $collectData);
            if ($res) {
                return $this->successJson('收藏成功', array('status' => 2));
            } else {
                return $this->errorJson('收藏失败', array('status' => 1));
            }
        }
    }

    /**
     * 穴位、疾病详情页收藏状态
     * @return \Illuminate\Http\JsonResponse
     */
    public function collectStatus()
    {
        //收藏状态
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;

        $id = intval(request()->get('id', 0));
        $to_type_id = intval(request()->get('to_type_id', 0));
        if (empty($id)) {
            return $this->errorJson('未发现收藏对象');
        }
        if (empty($to_type_id)) {
            return $this->errorJson('收藏类型to_type_id参数错误');
        }
        $collect = pdo_get('diagnostic_service_collect', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => $to_type_id, 'info_id' => $id));
        if (!$collect) {
            $data = [
                'status' => 1 //未收藏
            ];
            return $this->successJson('获取成功', $data);
        } else {
            $data = [
                'status' => 2  //已收藏
            ];
            return $this->successJson('获取成功', $data);
        }
    }

    /**
     * 用户收藏数量统计
     * @return mixed
     */
    public function userCollectCount()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;

        //穴位收藏数
        $acupointCollectCount = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_collect') . " WHERE user_id = :user_id AND uniacid = :uniacid AND to_type_id = 1 ", array(':user_id' => $user_id, ':uniacid' => $uniacid)));
        //病例收藏数
        $caseCollectCount = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_collect') . " WHERE user_id = :user_id AND uniacid = :uniacid AND to_type_id = 2 ", array(':user_id' => $user_id, ':uniacid' => $uniacid)));
        //文章收藏数
        $articleCollectCount = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_collect') . " WHERE user_id = :user_id AND uniacid = :uniacid AND to_type_id = 3 ", array(':user_id' => $user_id, ':uniacid' => $uniacid)));
        //芸众商品收藏数
        $goodsCollectCount = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('yz_member_favorite') . " WHERE member_id = :user_id AND  deleted_at is null ", array(':user_id' => $user_id)));

        return $this->successJson('收藏数获取成功', compact('acupointCollectCount', 'caseCollectCount', 'articleCollectCount', 'goodsCollectCount'));
    }
}
