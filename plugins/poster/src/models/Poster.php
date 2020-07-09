<?php

namespace Yunshop\Poster\models;

use app\common\models\BaseModel;
use app\common\models\MemberShopInfo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Yunshop\Poster\observers\PosterObserver;
use Illuminate\Validation\Rule;

class Poster extends BaseModel
{
    protected $table = 'yz_poster';
    protected $guarded = [''];
    protected $dates = ['deleted_at'];
    public $widgets =[];

    const TEMPORARY_POSTER = 1; //活动海报
    const FOREVER_POSTER = 2; //长期海报

    const IN_TIME = 1; //在"活动海报"限定的时间内
    const NOT_YET_START = 2; //活动还未开始
    const ALREADY_FINISHED = 3; //活动已经结束


    /**
     *  定义字段名
     * @return array */
    public function atributeNames() {
        return [
            'title' => '海报名称',
            'keyword' => '关键词',
            'style_data' => '海报设计',
            'response_title' => '推送标题',
            'response_thumb' => '推送封面',
            'response_desc' => '推送描述',
            'response_url' => '推送链接',
            'background' => '海报背景图',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {
        return [
            'uniacid' => 'required|integer',
            'title' => 'required|string|max:50',
            'keyword' => 'required',
            'style_data' => 'required',
            'type' => 'required|integer|between:1,2',
            'time_start' => 'nullable',
            'time_end' => 'nullable',
            'background' => 'required',
            'response_title' => 'nullable|string|max:50',
            'response_thumb' => 'nullable|max:255',
            'response_desc' => 'nullable|string|max:255',
            'response_url' => 'nullable|url|max:255',
            'is_open' => 'integer|between:0,1',
            'auto_sub' => 'integer|between:0,1',
            'status' => 'integer|between:0,1',
        ];
    }

    /**
     * 和辅表的一对一关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function supplement()
    {
        return $this->hasOne('Yunshop\Poster\models\PosterSupplement', 'poster_id', 'id');
    }

    /**
     * 一张海报对应多个奖励记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function award()
    {
        return $this->hasMany('Yunshop\Poster\models\PosterAward', 'poster_id', 'id');
    }

    /**
     * 一张海报对应多个扫码记录
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scan()
    {
        return $this->hasMany('Yunshop\Poster\models\PosterScan', 'poster_id', 'id');
    }

    /**
     * 获取当前公众号的所有海报 (不包括已删除的海报)
     * @return mixed
     */
    static public function getPosters()
    {
        return self::uniacid()
                    ->select(['id','title','type', 'keyword', 'status','center_show'])
                    ->withCount('award')
                    ->withCount('scan');
    }

    /**
     * 通过id获取海报
     * @param $id
     * @return mixed
     */
    static public function getPosterById($id)
    {
        return self::uniacid()->with(['supplement'])->find($id);
    }

    /**
     * 删除海报(软删除)
     * @param $id
     * @return mixed
     */
    static public function deletePoster($id)
    {

        return self::uniacid()
                    ->where('id', '=', $id)
                    ->delete();
    }

    /**
     * 搜索海报
     * @param $title 海报标题
     * @param $type 海报类型
     * @return mixed collection
     */
    static public function getPostersBySearch($title, $type)
    {
        $postersModel = self::uniacid()
                        ->select(['id','title','type', 'keyword', 'status','center_show'])
                        ->withCount('award')
                        ->withCount('scan');
        if (!empty($title) && !empty($type)){
            return $postersModel->where('title', 'like', '%'.$title.'%')
                                ->where('type', '=', $type);
        } else if (!empty($title) && empty($type)){
            return $postersModel->where('title', 'like', '%'.$title.'%');
        } else {
            return $postersModel->where('type', '=', $type);
        }
    }

    /**
     * 根据关键词获取海报
     * @param $keyword 海报关键词
     * @return mixed
     */
    static public function getPosterByKeyword($keyword)
    {
        $posterModel = static::uniacid()
            ->where('keyword', '=', $keyword)
            ->with(['supplement'])
            ->first();

        return $posterModel;
    }

    //检查发展下线的权限
    public function authorize(Poster $posterModel, $memberId)
    {
        //判断用户是否有发现下线的权限
        $agentId = MemberShopInfo::getMemberShopInfo($memberId)->agent_id;

        //todo
        //$memberId = \YunShop::app()->getMemberId();
        $isAgent = true;
        if (!$isAgent){
            return false;
        }

        //判断海报是否开启了扫码自动称为下线的设置
        $isOpen = $posterModel->is_open;
        if(!$isOpen){
            return false;
        }

        return true;
    }

    /**
     *在boot()方法里注册模型观察类
     * boot()和observe()方法都是从Model类继承来的
     * 主要是observe()来注册模型观察类，可以用TestMember::observe(new TestMemberObserve())
     * 并放在代码逻辑其他地方如路由都行，这里放在这个TestMember Model的boot()方法里自启动。
     */
    public static function boot()
    {
        parent::boot();
        //注册观察者
        static::observe(new PosterObserver());
    }


}