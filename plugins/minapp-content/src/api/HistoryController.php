<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;

//历史足迹控制器-wk 20210106
class HistoryController extends ApiController
{
    protected $ignoreAction = ['systemNotice','getVersion'];

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
     * 足迹记录接口 文章id/穴位id/病例id（文章，穴位，病例共用） 历史记录接口 历史记录信息 同收藏。
     * @return mixed
     */
    public function history()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $info_id = intval(request()->get('id', 0));
        $to_type_id = intval(request()->get('to_type_id', 0));
        if (empty($info_id)) {
            return $this->errorJson('浏览对象id参数不为空');
        }
        if (!$to_type_id) {
            return $this->errorJson('to_type_id参数不存在');
        }
        $show_date = date('Y-m-d', time());
        //足记数据
        $historyData = array(
            'user_id' => $user_id,
            'uniacid' => $uniacid,
            'info_id' => $info_id,
            'to_type_id' => $to_type_id,
            'add_time' => time(),
            'show_date' => $show_date
        );
        if ($to_type_id == 1) {
            //穴位记录信息
            $acupoint = pdo_get('diagnostic_service_acupoint', array('id' => $info_id));
            $historyData['title'] = $acupoint['name'];
            $historyData['description'] = $acupoint['get_position'];
            $historyData['image'] = $acupoint['image'];
        } elseif ($to_type_id == 2) {
            //病例记录信息
            $disease = pdo_get('diagnostic_service_disease', array('id' => $info_id));
            $historyData['title'] = $disease['name'];
            $historyData['description'] = $disease['description'];
            $historyData['image'] = $disease['image'];
        } elseif ($to_type_id == 3) {
            //文章记录信息
            $disease = pdo_get('diagnostic_service_disease', array('id' => $info_id));
            $historyData['title'] = $disease['name'];
            $historyData['description'] = $disease['description'];
            $historyData['image'] = $disease['image'];
            //文章收藏
            $article = pdo_get('diagnostic_service_article', array('id' => $info_id));
            $historyData['title'] = $article['title'];
            $historyData['description'] = $article['description'];
            $historyData['image'] = $article['thumb'];
        }
        $myHistory = pdo_get('diagnostic_service_history', array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => $to_type_id, 'info_id' => $info_id, 'show_date' => $show_date));
        if ($myHistory) {
            $res = pdo_update('diagnostic_service_history', array('add_time' => time()), array('user_id' => $user_id, 'uniacid' => $uniacid, 'to_type_id' => $to_type_id, 'info_id' => $info_id, 'show_date' => $show_date));
            if ($res) {
                return $this->successJson('足迹更新成功', $res);
            } else {
                return $this->errorJson('足迹更新失败');
            }
        } else {
            $res = pdo_insert('diagnostic_service_history', $historyData);
            if ($res) {
                if ($to_type_id == 1) {
                    pdo_update('diagnostic_service_acupoint', array('browse_volume +=' => 1), array('id' => $info_id));
                } elseif ($to_type_id == 2) {
                    pdo_update('diagnostic_service_disease', array('browse_volume +=' => 1), array('id' => $info_id));
                }
                return $this->successJson('足迹记录成功', $res);
            } else {
                return $this->errorJson('足迹记录失败');
            }
        }
    }

    /**
     * 用户足记列表展示 获取用户 history 历史记录信息。
     * @return mixed
     */
    public function userHistory()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;

        $userHistory = pdo_getall('diagnostic_service_history', array('user_id' => $user_id, 'uniacid' => $uniacid), array('info_id', 'title', 'description', 'image', 'show_date', 'to_type_id'), '', 'add_time DESC');
        $data = [];
        foreach ($userHistory as $k => $v) {
            $data[$v['show_date']][] = $v;
        }
        krsort($data);
        $lists = [];
        foreach ($data as $key => $vo) {
            $lists[]['day'] = $key;
            $lists[]['list'] = $vo;
        }

        return $this->successJson('获取用户足迹信息成功', $lists);
    }

    /**
     * 清除用户记录 清除用户足迹
     * @return mixed
     */
    public function deleteUserHistory()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;

        $result = pdo_delete('diagnostic_service_history', array('user_id' => $user_id, 'uniacid' => $uniacid));
        if ($result) {
            return $this->successJson('清除成功', array('status' => 1));
        } else {
            return $this->errorJson('清除失败', array('status' => 0));
        }
    }

    /**
     * 用户个人中心信息统计数
     * @return mixed
     */
    public function userDataStatistics()
    {
        //用户收藏数、足记数、关注数、粉丝数
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        //用户收藏数量
        $userCollectCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_collect') . " WHERE user_id = :user_id AND uniacid = :uniacid", array(':user_id' => $user_id, ':uniacid' => $uniacid));
        $userGoodsCollectCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('yz_member_favorite') . " WHERE member_id = :user_id AND  deleted_at is null ", array(':user_id' => $user_id));
        //用户历史记录数
        $userHistoryCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_history') . " WHERE user_id = :user_id AND uniacid = :uniacid", array(':user_id' => $user_id, ':uniacid' => $uniacid));
        //用户商品历史记录
        $userHistoryGoodsCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('yz_member_history') . " WHERE member_id = :user_id AND  deleted_at is null", array(':user_id' => $user_id));
        //用户关注数(此用户关注了那些用户）
        $userFollowCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_user_follow') . " WHERE user_id = :user_id AND uniacid = :uniacid", array(':user_id' => $user_id, ':uniacid' => $uniacid));
        //用户粉丝数（关注者id是此用户id)
        $userFansCount = pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_user_follow') . " WHERE fans_id = :user_id AND uniacid = :uniacid", array(':user_id' => $user_id, ':uniacid' => $uniacid));

        $dataStatistics = array(
            'collectCount' => intval($userCollectCount + $userGoodsCollectCount),
            'historyCount' => intval($userHistoryCount + $userHistoryGoodsCount),
            'followCount' => intval($userFollowCount),
            'fansCount' => intval($userFansCount),
        );

        return $this->successJson('成功获取用户统计数据', $dataStatistics);
    }

    /**
     *小贴士/通知
     * @return mixed
     */
    public function systemNotice()
    {
        $uniacid = $this->uniacid;
        $cache_key = 'ajy_system_notices' . $uniacid;
        $notices = cache_load($cache_key);
        if (!$notices) {
            $notices = pdo_getall('diagnostic_service_system_notice', array('uniacid' => $uniacid, 'status' => 1), array('id', 'title', 'content', 'jumpurl', 'jumptype','appid'), '', 'list_order DESC');
            cache_write($cache_key, $notices);
        }

        return $this->successJson('获取系统通知成功', $notices);
    }

    /**
     * 版本号获取
     * @return \Illuminate\Http\JsonResponse
     */
    public function getVersion()
    {
        $version = '1.0.0.0';
        return $this->successJson('获取成功',$version);
    }
}
