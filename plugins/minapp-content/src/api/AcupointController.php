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

//穴位|经络控制器-wk 20210105
class AcupointController extends ApiController
{
    protected $ignoreAction = ['getMeridian', 'getSortAcupoint', 'getAcupointInfo', 'acupointCommentList'];

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
        if (!$meridian[0]) {
            $meridian = MeridianModel::where(['type_id' => $type_id, 'status' => 1, 'uniacid' => $this->uniacid])
                ->select('id', 'name', 'discription', 'content', 'video', 'audio', 'image', 'start_time', 'end_time', 'recommend_course')
                ->orderBy('list_order', 'DESC')
                ->get();
            if (isset($meridian[0])) {
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

    /**
     * 获取穴位详情
     * @return mixed
     */
    public function getAcupointInfo()
    {
        $id = intval(request()->get('id', 0));
        if (!$id) {//获取穴位具体信息
            return $this->errorJson('穴位id未发现');
        }
        $cache_key = 'acupotionInfo' . $this->uniacid . $id;
        $acupotionInfo = Cache::get($cache_key);
        if (!$acupotionInfo['id']) {
            $acupotionInfo = DB::table('diagnostic_service_acupoint')->where(['id' => $id, 'uniacid' => $this->uniacid])->first();
            $acupotionInfo['goods'] = array();
            $acupotionInfo['article'] = array();
            if ($acupotionInfo['recommend_goods']) {  //查询推荐商品
                $acupotionInfo['recommend_goods'] = explode(',', $acupotionInfo['recommend_goods']); //文章关联商品
                foreach ($acupotionInfo['recommend_goods'] as $k => $value) {
                    $recommend_goods = DB::table('yz_goods')->where(['id' => $value, 'uniacid' => $this->uniacid])->select('id', 'title', 'thumb', 'price', 'status', 'deleted_at')->first();
                    if ($recommend_goods['status'] == 1 && !$recommend_goods['deleted_at']) {
                        $acupotionInfo['goods'][] = $recommend_goods;
                    }
                }
            }
            //查询推荐穴位
            if ($acupotionInfo['recommend_article']) {
                $acupotionInfo['recommend_article'] = explode(',', $acupotionInfo['recommend_article']); //文章穴位管理
                foreach ($acupotionInfo['recommend_article'] as $k => $v) {
                    $recommend_article = DB::table('diagnostic_service_article')->where(['id' => $v, 'uniacid' => $this->uniacid])->select('id', 'title', 'description', 'thumb')->first();
                    $acupotionInfo['article'][] = $recommend_article;
                }
            }
            Cache::forget($cache_key);
            Cache::add($cache_key, $acupotionInfo, 60);
        }

        return $this->successJson('请求成功', $acupotionInfo);
    }

    /**
     * 穴位评论 穴位笔记
     * @return mixed
     */
    public function acupointComment()
    {
        $user_id = $this->user_id;
        //用户禁言
        $user = DiagnosticServiceUser::where('ajy_uid', $this->user_id)->first();
        if ($user->is_black == 1) {
            if ($user->black_end_time > time()) {
                response()->json([
                    'result' => 301,
                    'msg' => '您已被系统禁言！截止时间至：' . date('Y-m-d H:i:s', $user->black_end_time) . '申诉请联系管理员',
                    'data' => false,
                ], 200, ['charset' => 'utf-8'])->send();
                exit;
            } else {
                $user->is_black = 0;
                $user->black_content = '时间到期,自然解禁';
                $user->save();
            }
        }
        $uniacid = $this->uniacid;

        $acupoint_id = intval(request()->get('acupoint_id', 0));
        $content = trim(request()->get('content', ''));
        $images = html_entity_decode(trim(request()->get('images', '')));
        $parent_id = intval(request()->get('parent_id', 0));
        if (!$acupoint_id) {
            return $this->errorJson('穴位id不存在');
        }
        if (!$content) {
            return $this->errorJson('内容不能为空');
        }
        $acupoint = DB::table('diagnostic_service_acupoint')->where(['id' => $acupoint_id])->first();
        if (!$acupoint) {
            return $this->errorJson('穴位不存在或已被删除');
        }
        // 评论内容敏感词过滤
        $wxapp_base_service = new BaseService();
        $sensitive_check = $wxapp_base_service->msgSecCheck($content);
        if (!is_bool($sensitive_check) || $sensitive_check === false) {
            return $this->errorJson('评论包含敏感内容', $sensitive_check);
        }
        $content_check = $wxapp_base_service->textCheck($content);
        $data = [
            'user_id' => $user_id,
            'uniacid' => $uniacid,
            'acupoint_id' => $acupoint_id,
            'content' => $content,
            'images' => $images,
            'create_time' => TIMESTAMP
        ];
        if ($content_check) {
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }

        if ($parent_id) {
            $data['parent_id'] = $parent_id;
            $data['is_reply'] = 1;
            $par_data = DB::table('diagnostic_service_acupoint_comment')->where(['id' => $parent_id])->first();
            if (!empty($par_data)) {
                $data['rele_user_id'] = $par_data['user_id'];
            }
            if ($user_id == $par_data['user_id']) {
                $data['mess_status'] = 1;
            }
        }
        $comment_id = DB::table('diagnostic_service_acupoint_comment')->insertGetId($data);
        if (!$comment_id) {
            return $this->errorJson('评论失败');
        }
        $newComment = DB::table('diagnostic_service_acupoint_comment')->where(['id' => $comment_id])->first();
        $newComment['images'] = json_decode($newComment['images'], true);
        if ($newComment['status'] == 0) {
            return $this->errorJson('评论内容涉及违规敏感词，请等待后台审核', array('status' => 0));
        }
        DB::table('diagnostic_service_acupoint')->where(['id' => $acupoint_id])->increment('comment_nums');
        return $this->successJson('评论成功', array('status' => 1, 'newComment' => $newComment));
    }

    /**
     * 穴位笔记和回复列表
     * @return mixed
     */
    public function acupointCommentList()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $acupoint_id = intval(request()->get('acupoint_id', 0)); //穴位id
        if (!$acupoint_id) {
            return $this->errorJson('穴位id不能为空');
        }

        $pindex = intval(request()->get('page', 1)); //初始页
        $psize = 10; //每页条数
        $query = load()->object('query');
        $acupointComments = $query->from('diagnostic_service_acupoint_comment', 'c')
            ->select('c.id', 'c.user_id', 'u.nickname', 'u.avatarurl', 'u.province', 'c.content', 'c.images', 'c.create_time')
            ->leftjoin('diagnostic_service_user', 'u')
            ->on('c.user_id', 'u.ajy_uid')
            ->where(array('c.uniacid' => $uniacid, 'c.acupoint_id' => $acupoint_id, 'c.is_reply' => 0, 'c.status' => 1))
            ->orderby(array('c.display_order' => 'DESC', 'c.create_time' => 'DESC'))
            ->page($pindex, $psize)
            ->getall();

        $total = intval($query->getLastQueryTotal()); //总条数
        $total_page = intval(($total + $psize - 1) / $psize); //总页数
        foreach ($acupointComments as $k => $v) {
            $acupointComments[$k]['images'] = json_decode($v['images'], true);
            Carbon::setLocale('zh');
            $acupointComments['time'] = Carbon::createFromTimestamp($v['create_time'])->diffForHumans();
            $like = pdo_get('diagnostic_service_acupoint_comment_like', array('user_id' => $user_id, 'comment_id' => $v['id']));
            if ($like) {
                $acupointComments[$k]['is_like'] = 2;  //已点赞
            } else {
                $acupointComments[$k]['is_like'] = 1;  //未点赞
            }
            $like_nums = intval(pdo_fetchcolumn("SELECT COUNT(*) FROM " . tablename('diagnostic_service_acupoint_comment_like') . " WHERE comment_id = :comment_id ", array(':comment_id' => $v['id'])));
            $acupointComments[$k]['like_nums'] = $like_nums;
            //回复列表
            $reply = $query->from('diagnostic_service_acupoint_comment', 'c')
                ->select('c.id', 'c.user_id', 'u.nickname', 'u.avatarurl', 'u.province', 'c.content', 'c.create_time')
                ->leftjoin('diagnostic_service_user', 'u')
                ->on('c.user_id', 'u.ajy_uid')
                ->where(array('c.uniacid' => $uniacid, 'c.parent_id' => $v['id'], 'c.is_reply' => 1, 'c.status' => 1))
                ->orderby('c.create_time', 'DESC')
                ->getall();
            foreach ($reply as $key => $value) {
                $reply[$key]['time'] = Carbon::createFromTimestamp($value['create_time'])->diffForHumans();
            }
            $acupointComments[$k]['reply_nums'] = intval($query->getLastQueryTotal());
            $acupointComments[$k]['reply'] = $reply;
        }

        return $this->successJson('请求成功', compact('acupointCommentCount', 'total', 'total_page', 'acupointComments'));
    }

    /**
     * 穴位笔记/回复删除
     * @return mixed
     */
    public function acupointCommentDel()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $id = intval(request()->get('id', 0));
        if (!$id) {
            return $this->errorJson('评论id不存在', array('status' => 0));
        }
        $comment = DB::table('diagnostic_service_acupoint_comment')->where(['id' => $id])->first();
        if (!$comment) {
            return $this->errorJson('该评论不存在或已被删除', array('status' => 0));
        }
        if ($comment['user_id'] != $user_id) {
            return $this->errorJson('不是你的评论,无法删除', array('status' => 0));
        }
        $reply_nums = 0;//主评查出子评论个数
        if ($comment['is_reply'] == 0) {
            $reply_nums = DB::table('diagnostic_service_acupoint_comment')->where(['parent_id' => $id, 'status' => 1])->count();
        }
        $reply_nums += 1;
        $res = DB::table('diagnostic_service_acupoint_comment')->where('id', $id)->delete();  //删除操作
        if ($res) {
            DB::table('diagnostic_service_acupoint_comment')->where('parent_id', $id)->delete();  //删除评论子评论
            DB::table('diagnostic_service_acupoint')->where('id', $comment['acupoint_id'])->decrement('comment_nums', $reply_nums);
            return $this->successJson('删除成功', array('status' => 1, 'nums' => $reply_nums));
        } else {
            return $this->errorJson('删除失败', array('status' => 0));
        }
    }

    /**
     * 穴位笔记（主评）点赞 (暂未调用）
     * @return \Illuminate\Http\JsonResponse
     */
    public function acupointCommentLike()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $comment_id = intval(request()->get('comment_id', 0));
        if (!$comment_id) {
            return $this->errorJson('主评id不存在', array('status' => 0));
        }
        $like = pdo_get('diagnostic_service_acupoint_comment_like', array('user_id' => $user_id, 'comment_id' => $comment_id, 'uniacid' => $uniacid));
        if ($like) {
            //已点赞，取消点赞
            $res = pdo_delete('diagnostic_service_acupoint_comment_like', array('user_id' => $user_id, 'comment_id' => $comment_id, 'uniacid' => $uniacid));
            if ($res) {
                return $this->successJson('取消点赞成功', array('status' => 1, 'is_like' => 1));
            } else {
                return $this->errorJson('取消点赞失败', array('status' => 0, 'is_like' => 2));
            }
        } else {
            $data = [
                'uniacid' => $uniacid,
                'user_id' => $user_id,
                'comment_id' => $comment_id,
                'create_time' => time()
            ];
            $res = pdo_insert('diagnostic_service_acupoint_comment_like', $data);
            if ($res) {
                return $this->successJson('点赞成功', array('status' => 1, 'is_like' => 2));
            } else {
                return $this->errorJson('点赞失败', array('status' => 0, 'is_like' => 1));
            }
        }
    }
}
