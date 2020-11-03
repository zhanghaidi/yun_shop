<?php

namespace Yunshop\MinApp\Common\Models;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Popup
 * @package Yunshop\MinApp\models\Popup
 * @property int id
 * @property int uniacid
 * @property int position_id
 * @property string title
 * @property string picture
 * @property string web_link
 * @property string pagepath
 * @property int is_show
 * @property int show_rule
 * @property Carbon start_time
 * @property Carbon end_time
 */

class Popup extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_popup";
    public $timestamps = false;
    public $dates = ['deleted_at'];
    protected $guarded = [''];
    protected $mediaFields = ['picture'];
    protected $casts = ['start_time' => 'date','end_time' => 'date','created_at'=>'date'];
    protected $appends = [];
    protected static $showRule = ['无','每次进入首页显示','每日首次登录显示'];

    //默认值
    public $attributes = [
        'web_link' => '',
        'pagepath' => '',
        'is_show' => 0,
        'sort' => 0,
    ];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'id' => '弹窗ID',
            'uniacid' => '公众号 ID',
            'position_id' => '弹窗位置 ID',
            'title' => '标题',
            'picture' => '图片',
            'web_link' => '网页链接',
            'pagepath' => '小程序路径',
            'is_show' => '是否显示',
            'show_time' => '展示时间',
            'show_rule' => '显示规则',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'sort' => '排序字段',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'integer',
            'uniacid' => 'required|integer',
            'position_id' => 'required|integer',
            'title' => 'required|string',
            'picture' => 'required|string',
            'web_link' => 'string',
            'pagepath' => 'string',
            'show_time' => 'required|integer|min:1',
            'is_show' => 'required|integer',
            'show_rule' => 'integer',
            'start_time' => 'required|integer',
            'end_time' => 'required|integer',
            'sort' => 'integer',
        ];
    }

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->where('uniacid', \YunShop::app()->uniacid)->with('belongsToPosition');

        if (!empty($search['position_id'])) {
            $query->where('position_id', $search['position_id']);
        }

        if (!empty($search['id'])) {
            $query->where('id','=', $search['id']);
        }

        if (!empty($search['title'])) {
            $query->where('title', 'like' , '%' . $search['title'] . '%');
        }
        return $model;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongsToPosition()
    {
        return $this->belongsTo('Yunshop\MinApp\Common\Models\PopupPositon', 'position_id', 'id');
    }

    public static function handleArray($data, $id)
    {
        $data['uniacid'] = \YunShop::app()->uniacid;

        if($id){
            $data['id'] = $id;
            $data['updated_at'] = time();
        }else{
            $data['created_at'] = time();
        }

        if(!empty($data['time']) && $data['time']['start'] != '请选择' && $data['time']['end'] != '请选择'){
            $data['start_time'] = strtotime($data['time']['start']);
            $data['end_time'] = strtotime($data['time']['end']);
        }

        return array_except($data,['time']);
    }

    public function getPopupById($id){
        return self::uniacid()->where(id, $id);
    }

    public static function getShowRule(){
        return self::$showRule;
    }

}
