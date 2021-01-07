<?php

namespace Yunshop\MinappContent\api;

use app\common\components\ApiController;

//消息控制器-wk 20210107
class MessageController extends ApiController
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
     * 用户评论未读信息列表
     * @return mixed
     */
    public function messageList()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $mess_status = request()->input('mess_status', 0);
        $page = request()->input('page', 0);
        $pagesize = 10;

        $query = load()->object('query');
        if (!empty($mess_status) && $mess_status == 1) {
            //fixBy-wk-20201211 敏感评论隐藏 status = 1
            $data = $query->from('diagnostic_service_post_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0, 'status' => 1])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
            $data1 = $query->from('diagnostic_service_article_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0, 'status' => 1])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
            foreach ($data as $k => $v) {
                pdo_update('diagnostic_service_post_comment', array('mess_status' => 1), array('id' => $v['id']));
            }
            foreach ($data1 as $k => $v) {
                pdo_update('diagnostic_service_article_comment', array('mess_status' => 1), array('id' => $v['id']));
            }
            return $this->successJson('状态更新成功', []);
        }

        $data1 = $query->from('diagnostic_service_post_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'del_sta' => 0, 'status' => 1])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
        $data2 = $query->from('diagnostic_service_article_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'del_sta' => 0, 'status' => 1])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
        $data3 = array_merge($data1, $data2);
        if (count($data3) >= 2) {
            $time = array_column($data3, 'create_time');
            array_multisort($time, SORT_DESC, $data3);
        }
        $user_array = [];
        $post_array = [];
        $article_array = [];
        if (!empty($data3)) {
            foreach ($data3 as $k => $v) {
                $user_data = array_key_exists($v['user_id'], $user_array) ? $user_array[$v['user_id']] : null;
                if (!$user_data) {
                    $user_data = pdo_get('diagnostic_service_user', array('ajy_uid' => $v['user_id']));
                    $user_array[$v['user_id']] = $user_data;
                }
                $post_data = array_key_exists($v['post_id'], $post_array) ? $post_array[$v['post_id']] : null;
                if (!$post_data) {
                    $post_data = pdo_get('diagnostic_service_post', array('id' => $v['post_id']));
                    $post_array[$v['post_id']] = $post_data;
                }
                $datas[$k]['nickname'] = $user_data['nickname'];
                $datas[$k]['avatar'] = $user_data['avatarurl'];
                $datas[$k]['content'] = $v['content'];
                $datas[$k]['create_time'] = $this->dataarticletime($v['create_time']);
                $datas[$k]['mess_status'] = $v['mess_satatus'];
                $datas[$k]['reply_type'] = 0;   //0:普通文章，1：辟谣
                $datas[$k]['comm_id'] = $v['id'];
                if ($v['parent_id'] != 0) {
                    $datas[$k]['type'] = 1;    //二级回复
                } else {
                    $datas[$k]['type'] = 0;    //一级回复（也就是直接回复）
                }
                if (isset($v['article_id'])) {
                    $article_data = array_key_exists($v['article_id'], $article_array) ? $article_array[$v['article_id']] : null;
                    if (!$article_data) {
                        $article_data = pdo_get('diagnostic_service_article', array('id' => $v['article_id']));
                        $article_array[$v['article_id']] = $article_data;
                    }
                    $datas[$k]['article_id'] = $v['article_id'];
                    $datas[$k]['urlpath'] = $article_data['thumb'];
                } else {
                    if ($post_data['article_id'] != 0) {
                        $datas[$k]['post_id'] = $v['post_id'];
                        $article_data = array_key_exists($post_data['article_id'], $article_array) ? $article_array[$post_data['article_id']] : null;
                        if (!$article_data) {
                            $article_data = pdo_get('diagnostic_service_article', array('id' => $post_data['article_id']));
                            $article_array[$post_data['article_id']] = $article_data;
                        }
                        if (!empty($article_data)) {
                            $datas[$k]['urlpath'] = $article_data['thumb'];
                            $datas[$k]['title'] = $article_data['title'];
                            $datas[$k]['reply_type'] = 1;
                        } else {
                            $count = mb_strlen($post_data['images']);
                            if ($count != 2 && $count != 0) {
                                $str = $post_data['images'];
                                preg_match_all("/(?:\[\")(.*)(?:\"\])/i", $str, $match);
                                $image = $match[1][0];
                                $image = explode(',', $image);
                                $match = $image[0];
                                if (strstr($match, '"')) {
                                    $match = str_replace('"', "", $match);
                                } else {
                                    $match = $image[0];
                                };
                            } else {
                                $match = $post_data['video_thumb'];
                                $datas[$k]['board_id'] = $post_data['board_id'];
                            }
                            $datas[$k]['urlpath'] = $match;
                        }
                    } else {
                        $datas[$k]['post_id'] = $v['post_id'];
                        $count = mb_strlen($post_data['images']);
                        if ($count != 2 && $count != 0) {
                            $str = $post_data['images'];
                            preg_match_all("/(?:\[\")(.*)(?:\"\])/i", $str, $match);
                            $image = $match[1][0];
                            $image = explode(',', $image);
                            $match = $image[0];
                            if (strstr($match, '"')) {
                                $match = str_replace('"', "", $match);
                            } else {
                                $match = $image[0];
                            };
                        } else {
                            $match = $post_data['video_thumb'];
                            $datas[$k]['board_id'] = $post_data['board_id'];
                        }

                        $datas[$k]['urlpath'] = $match;
                    }

                }

            }
        } else {
            return $this->successJson('暂无数据', []);
        }
        return $this->successJson('信息获取成功', $datas);
    }

    /**
     * 关注消息显示
     * @return mixed
     */
    public function followMessageList()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $mess_status = request()->input('mess_status', 0);
        $page = request()->input('page', 0);
        $pagesize = 10;

        $query = load()->object('query');
        if (!empty($mess_status) && $mess_status == 2) {
            $data = $query->from('diagnostic_service_user_follow', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
            foreach ($data as $k => $v) {
                pdo_update('diagnostic_service_user_follow', array('mess_status' => 1), array('id' => $v['id']));
            }
            return $this->successJson('状态更新成功', []);
        }
        $data = $query->from('diagnostic_service_user_follow', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'del_sta' => 0])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $user_data = pdo_get('diagnostic_service_user', array('ajy_uid' => $v['user_id']));
                $datas[$k]['nickname'] = $user_data['nickname'];
                $datas[$k]['avatar'] = $user_data['avatarurl'];
                $datas[$k]['user_id'] = $v['user_id'];
                $datas[$k]['create_time'] = $this->dataarticletime($v['create_time']);
                $datas[$k]['mess_status'] = $v['mess_satatus'];
                $datas[$k]['foll_id'] = $v['id'];
                $user_follow = pdo_get('diagnostic_service_user_follow', array('uniacid' => $uniacid, 'user_id' => $user_id, 'fans_id' => $v['user_id']));
                if (!empty($user_follow)) {
                    $datas[$k]['status'] = 1;
                } else {
                    $datas[$k]['status'] = 0;
                }
            }
            return $this->successJson('信息获取成功', $datas);
        } else {
            return $this->successJson('暂无数据', []);
        }
    }

    /**
     * 点赞消息显示
     * @return mixed
     */
    public function likeMessageList()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $mess_status = request()->input('mess_status', 0);
        $page = request()->input('page', 0);
        $pagesize = 10;

        $query = load()->object('query');
        if (!empty($mess_status) && $mess_status == 3) {
            $data = $query->from('diagnostic_service_post_like', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
            foreach ($data as $k => $v) {
                pdo_update('diagnostic_service_post_like', array('mess_status' => 1), array('id' => $v['id']));
            }
            return $this->successJson('状态更新成功', []);
        }
        $data = $query->from('diagnostic_service_post_like', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'del_sta' => 0])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $user_data = pdo_get('diagnostic_service_user', array('ajy_uid' => $v['user_id']));
                $post_data = pdo_get('diagnostic_service_post', array('id' => $v['post_id']));
                $datas[$k]['nickname'] = $user_data['nickname'];
                $datas[$k]['avatar'] = $user_data['avatarurl'];
                $datas[$k]['create_time'] = $this->dataarticletime($v['create_time']);
                $datas[$k]['mess_status'] = $v['mess_satatus'];
                $datas[$k]['like_id'] = $v['id'];
                $datas[$k]['reply_type'] = 0;
                if ($post_data['article_id'] != 0) {
                    $datas[$k]['post_id'] = $v['post_id'];
                    $article_data = pdo_get('diagnostic_service_article', array('id' => $post_data['article_id']));
                    $datas[$k]['urlpath'] = $article_data['thumb'];
                    $datas[$k]['title'] = $article_data['title'];
                    $datas[$k]['reply_type'] = 1;
                } else {
                    $count = mb_strlen($post_data['images']);
                    if ($count != 2 && $count != 0) {
                        $str = $post_data['images'];
                        preg_match_all("/(?:\[\")(.*)(?:\"\])/i", $str, $match);
                        $image = $match[1][0];
                        $image = explode(',', $image);
                        $match = $image[0];
                        if (strstr($match, '"')) {
                            $match = str_replace('"', "", $match);
                        } else {
                            $match = $image[0];
                        };
                    } else {
                        $match = $post_data['video_thumb'];
                        $datas[$k]['board_id'] = $post_data['board_id'];
                    }
                    $datas[$k]['urlpath'] = $match;
                    $datas[$k]['post_id'] = $v['post_id'];
                }

            }
            return $this->successJson('信息获取成功', $datas);
        } else {
            return $this->successJson('暂无数据', []);
        }
    }

    /**
     * 关注数量相关
     * @return \Illuminate\Http\JsonResponse
     */
    public function followMessageCount()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $query = load()->object('query');
        $count_comment = $query->from('diagnostic_service_post_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0, 'status' => 1])->orderby('create_time DESC')->count();   //评论数量
        $count_like = $query->from('diagnostic_service_post_like', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->orderby('create_time DESC')->count();         //点赞数量
        $count_follow = $query->from('diagnostic_service_user_follow', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->orderby('create_time DESC')->count();     //关注数量
        $article_comment = $query->from('diagnostic_service_article_comment', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0, 'status' => 1])->orderby('create_time DESC')->count();   //辟谣评论
        $count_order = $query->from('yz_order_messages', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->orderby('create_time DESC')->count();
        $data = [
            'count_comment' => $count_comment + $article_comment,      //评论总数量
            'count_like' => $count_like,                               //点赞
            'count_follow' => $count_follow,                           //关注
            'count_order' => $count_order,                               //发货，退款
            'all_count' => $count_comment + $count_like + $count_follow + $article_comment,
            'all_counts' => $count_comment + $count_like + $count_follow + $article_comment + $count_order,
        ];

        return $this->successJson('获取成功', $data);
    }

    /**
     * 芸众订单相关 消息
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderMessageList()
    {
        $user_id = $this->user_id;
        $uniacid = $this->uniacid;
        $mess_status = request()->input('mess_status', 0);
        $page = request()->input('page', 0);
        $pagesize = 10;

        $query = load()->object('query');
        if (!empty($mess_status) && $mess_status == 4) {
            $data = $query->from('yz_order_messages', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id, 'mess_status' => 0])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
            foreach ($data as $k => $v) {
                pdo_update('yz_order_messages', array('mess_status' => 1), array('id' => $v['id']));
            }
            return $this->successJson('状态更新成功', []);
        }
        $data = $query->from('yz_order_messages', 'u')->where(['uniacid' => $uniacid, 'rele_user_id' => $user_id])->limit($page * $pagesize, $pagesize)->orderby('create_time DESC')->getall();
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                $goods_data = pdo_get('yz_order_goods', array('order_id' => $v['order_id']));
                $datas[$k]['order_sn'] = $v['order_sn'];
                $datas[$k]['type'] = $v['type'];
                $datas[$k]['create_time'] = date('Y-m-d h:i:s', $v['create_time']);
                $datas[$k]['order_id'] = $v['order_id'];
                $datas[$k]['thumb'] = $goods_data['thumb'];
                $datas[$k]['goods_title'] = $goods_data['title'];
            }
            return $this->successJson('信息获取成功', $datas);
        } else {
            return $this->successJson('暂无数据', []);
        }
    }

    /**
     * 消息删除
     * @return \Illuminate\Http\JsonResponse
     */
    public function delMessage()
    {
        $del_type = request()->input('del_type', 0);
        if (!$del_type) {
            return $this->errorJson('删除类型不能为空');
        }
        $mess_id = request()->input('mess_id', 0);
        if (!$mess_id) {
            return $this->errorJson('消息id不能为空');
        }
        if (!empty($del_type) && !empty($mess_id)) {
            switch ($del_type) {
                case "1":
                    pdo_update('diagnostic_service_post_comment', array('del_sta' => 1), array('id' => $mess_id));  //评论
                    break;
                case "2":
                    pdo_update('diagnostic_service_article_comment', array('del_sta' => 1), array('id' => $mess_id));  //文章评论
                    break;
                case "3":
                    pdo_update('diagnostic_service_user_follow', array('del_sta' => 1), array('id' => $mess_id));   //关注
                    break;
                case "4":
                    pdo_update('diagnostic_service_post_like', array('del_sta' => 1), array('id' => $mess_id));    //点赞
                    break;
                default:
                    echo "数据类型有误！";
            }
            return $this->successJson('信息删除成功', []);
        }
    }
}
