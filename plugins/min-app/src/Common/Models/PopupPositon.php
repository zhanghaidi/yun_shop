<?php

namespace Yunshop\MinApp\Common\Models;

use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class PopupPositon
 * @package Yunshop\MinApp\models\PopupPositon
 * @property int id
 * @property int uniacid
 * @property string position_name
 * @property int weapp_account_id
 * @property int type
 * @property int is_show
 */

class PopupPositon extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_popup_position";
    public $timestamps = false;
    public $dates = ['deleted_at'];
    protected $guarded = [''];
    protected $casts = ['created_at' => 'date'];
    protected $appends = [];
    protected static $posType = ['首页弹窗','用户中心'];

    //默认值
    public $attributes = [
        'type' => 0,
        'is_show' => 0,
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
            'position_name' => '位置名称',
            'weapp_account_id' => '小程序账号ID',
            'type' => '位置类型',
            'is_show' => '是否显示',
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
            'position_name' => 'required|string',
            'weapp_account_id' => 'required|integer',
            'type' => 'required|integer',
            'is_show' => 'required|integer',
        ];
    }

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->where('uniacid', \YunShop::app()->uniacid);

        if (!empty($search['id'])) {
            $query->where('id','=', $search['id']);
        }

        if (!empty($search['account_id'])) {
            $query->where('weapp_account_id', $search['account_id']);
        }

        if (!empty($search['name'])) {
            $query->where('position_name', 'like' , '%' . $search['name'] . '%');
        }
        return $model;
    }

    public function getPositionById($id){
        return self::uniacid()->where(id, $id);
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

        return $data;
    }

    public static function getPosType(){
        return self::$posType;
    }

}
