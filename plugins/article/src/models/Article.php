<?php

namespace Yunshop\Article\models;

use app\common\models\BaseModel;
use Illuminate\Support\Facades\DB;
use Yunshop\ArticlePay\models\ArticleModel;
use Yunshop\ArticlePay\models\RecordModel;

class Article extends BaseModel
{
    public $table = "yz_plugin_article";
    protected $guarded = [''];
    public $liked;
    public $virtualAt;
    protected $appends = ['liked', 'virtual_at'];

    //默认值
    public $attributes = [
        'read_num' => 0,
        'like_num' => 0,
        'virtual_read_num' => 0,
        'virtual_like_num' => 0,
        'author' => '编辑小芸',
        'per_person_per_day' => 0,
        'total_per_person' => 0,
        'point' => 0,
        'credit' => 0,
        'bonus_total' => 0,
        'bonus_total_now' => 0,
        'advs' => '',
        'reward_mode' => 0,
    ];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'uniacid' => '公众号ID',
            'category_id' => '文章分类',
            'title' => '文章标题',
            'desc' => '文章封面描述',
            'thumb' => '文章封面图片',
            'content' => '文章内容',
            'virtual_created_at' => '虚拟发布时间',
            'author' => '作者',
            'virtual_read_num' => '虚拟阅读数',
            'virtual_like_num' => '虚拟点赞数',
            'link' => '链接',
            'per_person_per_day' => '每人每天奖励次数限制',
            'total_per_person' => '每人总共奖励次数限制',
            'point' => '积分奖励',
            'credit' => '余额奖励',
            'bonus_total' => '最高累计奖金限制',
            'no_copy_url' => '禁止复制链接',
            'no_share' => '禁止分享至朋友圈',
            'no_share_to_friend' => '禁止分享给朋友',
//            'keyword' => '关键字',
            'report_enabled' => '举报按钮是否显示',
            'advs_type' => '推广产品显示设置',
            'advs_title' => '推广产品标题',
            'advs_title_footer' => '推广产品底部文字',
            'advs_link' => '推广产品底部链接',
            'state' => '状态是否启用',
            'reward_mode' => '奖励方式',
//            'state_wechat' => '微信端显示开关',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'uniacid' => 'required|integer',
            'category_id' => 'required|integer|min:1',
            'title' => 'required|max:50',
            'desc' => 'max:255',
            'thumb' => 'string',
            'content' => 'required',
            'virtual_created_at' => 'numeric',
            'author' => 'required',
            'virtual_read_num' => 'numeric',
            'virtual_like_num' => 'numeric',
            'link' => 'url',
            'per_person_per_day' => 'numeric',
            'total_per_person' => 'numeric',
            'point' => 'numeric',
            'credit' => 'numeric',
            'bonus_total' => 'numeric',
            'no_copy_url' => 'digits_between:0,1',
            'no_share' => 'digits_between:0,1',
            'no_share_to_friend' => 'digits_between:0,1',
//            'keyword' => 'required|string|max:50',
            'report_enabled' => 'required|digits_between:0,1',
            'advs_type' => 'numeric',
            'advs_title' => 'string|max:50',
            'advs_title_footer' => 'string|max:50',
            'advs_link' => 'url',
            'advs' => 'json',
            'state' => 'required|digits_between:0,1',
            'reward_mode' => 'required|digits_between:0,1',
//            'state_wechat' => 'required|digits_between:0,1',
        ];
    }

    //关联模型
    public function belongsToCategory()
    {
        return $this->belongsTo('Yunshop\Article\models\Category', 'category_id', 'id');
    }


    public static function getArticlesWithEssentialContent($limit = NULL)
    {
        $res = self::uniacid()
            ->select()
            ->where('state', 1);

        if ($limit) {
            $res = $res->limit($limit);
        }

        return $res;
    }

    /**
     * 供后台使用, 提供所有文章(包括已经关闭)
     * @param $search
     * @return mixed
     */
    public static function getArticlesByTitle($search)
    {
        return self::uniacid()->where('title', 'like', '%' . $search . '%')->with('belongsToCategory');
    }

    /**
     * 供后台使用, 搜索文章
     * @return mixed
     */
    public static function getArticles()
    {
        return self::uniacid()->with('belongsToCategory');
    }

    /**
     * 根据文章id获取文章详情, 包括其所属分类的信息
     * @param $id
     * @return mixed
     */
    public static function getArticle($id,$member_id = 0)
    {
        $res =  self::uniacid()
            ->with(['belongsToCategory' => function ($query) {
                return $query->select('id', 'name');
            }]);

        if (app('plugins')->isEnabled('article-pay')) {
            $res->with(["hasOneArticlePay"=>function ($query){
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $res->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }
         return $res->where('id', $id)
            ->first();
    }

    public static function deletedArticle($id)
    {
        return self::uniacid()->where('id', $id)
            ->delete();
    }

    /**
     * 供后台使用, 提供文章(包括已经关闭)
     * @param $category_id
     * @param null $keyword
     * @return $this
     */
    public static function getArticlesBySearch($category_id, $keyword = NULL)
    {
        if (!empty($category_id) && !empty($keyword)) {
            $result =  self::uniacid()
                ->with('belongsToCategory')
                ->where('title', 'like', '%' . $keyword . '%')
                ->where('category_id', $category_id);
        } elseif (empty($category_id) && !empty($keyword)) {
            $result =  self::uniacid()
                ->with('belongsToCategory')
                ->where('title', 'like', '%' . $keyword . '%');
        } elseif (!empty($category_id) && empty($keyword)) {
            $result =  self::uniacid()
                ->with('belongsToCategory')
                ->where('category_id', $category_id);
        }
        return $result;
    }

    /**
     * 获取"所有"文章的基本信息 (用于列表展示, 但是不包含详情; 不包括已经关闭的文章)
     * @return mixed
     */
    public static function getArticleOverviews($member_id)
    {
        $model = self::uniacid()
            ->select('id', 'title', 'author', 'thumb', 'desc', 'category_id', 'updated_at', 'virtual_created_at', 'type',
            DB::raw('read_num + virtual_read_num as read_sum, like_num + virtual_like_num as like_sum'));

        if (app('plugins')->isEnabled('article-pay')) {
            $model->with(["hasOneArticlePay"=>function ($query) use ($member_id){
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $model->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        $model->with(['belongsToCategory' => function ($query) {
            return $query->select('id', 'name');
        }])
            ->where('state', 1)
            ->where('type', '!=', 1)
            ->orderBy('id', 'desc');

        return $model;

    }

    /**
     * 获取"指定分类"下的文章的基本信息 (用于列表展示, 但是不包含详情; 不包括已经关闭的文章)
     * @param $category_id
     * @return mixed
     */
    public static function getArticleOverviewsByCategory($category_id,$member_id)
    {
        $model = self::uniacid()
            ->select('id', 'title', 'author', 'thumb', 'desc', 'category_id', 'updated_at', 'virtual_created_at', 'type',
                DB::raw('read_num + virtual_read_num as read_sum, like_num + virtual_like_num as like_sum'));

        if (app('plugins')->isEnabled('article-pay')) {
            $model->with(["hasOneArticlePay"=>function ($query) use ($member_id){
                $query->where("status",0)->where("money",">",0)->select("article_id","money");
            }]);

            $model->with(["hasOneRecord"=>function ($query) use ($member_id){
                $query->where("member_id",$member_id)->where("pay_status",1)->select("article_id","pay_status");
            }]);
        }

        $model->with(['belongsToCategory' => function ($query) {
            return $query->select('id', 'name');
        }])
        ->where('state', 1)
        ->where('category_id', $category_id)
        ->where('type', '!=', 1)
        ->orderBy('id', 'desc');

        return $model;
    }

    public function getLikedAttribute()
    {
        if (!isset($this->liked)) {
            $this->liked = Log::getLogsById($this->id)->select('liked')->pluck('liked')->first();
        }
        return $this->liked;
    }

    public function getvirtualAtAttribute()
    {
        if (!isset($this->virtualAt)) {
            $this->virtualAt = date('Y-m-d',$this->virtual_created_at);
        }
        return $this->virtualAt;
    }

    public static function getAudioArticle($display_order)
    {
        if ($display_order == 1) {
            return self::uniacid()->with('belongsToCategory')->where('type', 1)->orderBy('display_order', 'desc')->orderBy('id', 'desc');
        }
        return self::uniacid()->with('belongsToCategory')->where('type', 1)->orderBy('display_order')->orderBy('id');
    }

    public function hasOneArticlePay()
    {
        return $this->hasOne(ArticleModel::class,'article_id','id');
    }

    public function hasOneRecord()
    {
        return $this->hasOne(RecordModel::class,'article_id','id');
    }
}
