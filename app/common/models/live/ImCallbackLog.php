<?php

namespace app\common\models\live;

use Carbon\Carbon;
use app\common\models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ImCallbackLog
 * @package app\common\models\live\IMCallbackLog
 * @property int id
 * @property int uniacid
 * @property int sdk_appid
 * @property tinyint type
 * @property string callback_command
 * @property string group_id
 * @property string from_account
 * @property string operator_account
 * @property Carbon msg_time
 * @property tinyint msg_type
 * @property string msg_content
 * @property string callback_data
 * @property string client_iP
 */
class ImCallbackLog extends BaseModel
{

    use SoftDeletes;

    public $table = "yz_im_callback_log";
    public $timestamps = false;
    public $dates = ['deleted_at'];
    protected $guarded = [''];
    protected $casts = ['msg_time' => 'date', 'updated_at' => 'date', 'created_at' => 'date'];
    protected $appends = ['type_parse','msg_type_parse','msg_content_parse'];

    protected static $type = [['', '未知'], ['State', '在线状态'], ['Sns', '资料关系链'], ['C2C', '单聊消息'], ['Group', '群组系统']];
    protected static $msgType = [['', '未知'], ['TIMTextElem', '文本消息'], ['TIMLocationElem', '地理位置消息'], ['TIMFaceElem', '表情消息'], ['TIMCustomElem', '自定义消息'], ['TIMSoundElem', '语音消息'], ['TIMImageElem', '图像消息'], ['TIMFileElem', '文件消息'], ['TIMVideoFileElem', '视频消息']];

    //默认值
    public $attributes = [
        'type' => 0,
        'msg_type' => 0,
    ];

    /**
     * 自定义字段名
     * 可使用
     * @return array
     */
    public function atributeNames()
    {
        return [
            'id' => 'ID',
            'uniacid' => '公众号 ID',
            'sdk_appid' => 'SDKAppID',
            'type' => '回调类型',
            'callback_command' => '回调命令字',
            'group_id' => '群组ID',
            'from_account' => '发送者',
            'operator_account' => '请求的发起者',
            'msg_time' => '消息的时间',
            'msg_type' => '消息类型',
            'msg_content' => '消息内容',
            'callback_data' => '回调内容',
            'client_iP' => '客户端IP地址',
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
            'sdk_appid' => 'required',
            'type' => 'required|integer',
            'callback_command' => 'required|string',
            'group_id' => 'string',
            'from_account' => 'string',
            'operator_account' => 'string',
            'msg_time' => 'required|integer',
            'msg_type' => 'required|integer',
            'msg_content' => 'string',
            'callback_data' => 'string',
            'client_iP' => 'string',
        ];
    }

    public function getTypeParseAttribute()
    {
        return $this->parseType($this->type);
    }

    public function getMsgTypeParseAttribute()
    {
        return $this->parseMsgType($this->msg_type);
    }

    public function getMsgContentParseAttribute()
    {
        return $this->parseMsgContent($this->msg_content);
    }

    public function scopeSearch(Builder $query, $search)
    {
        $model = $query->where('uniacid', \YunShop::app()->uniacid);

        if (!empty($search['callback_command'])) {
            $query->where('callback_command', 'like', '%' . trim($search['callback_command']) . '%');
        }

        if (!empty($search['group_id'])) {
            $query->where('group_id', '=', trim($search['group_id']));
        }

        if ($search['is_time']) {
            if ($search['time']['start'] != '请选择' && $search['time']['end'] != '请选择') {
                $range = [strtotime($search['time']['start']), strtotime($search['time']['end'])];
                $model->whereBetween('created_at', $range);
            }
        }

        return $model;
    }

    public static function handleArray($data, $id)
    {
        $data['uniacid'] = \YunShop::app()->uniacid;

        if ($id) {
            $data['id'] = $id;
            $data['updated_at'] = time();
        } else {
            $data['created_at'] = time();
        }

        if (!empty($data['time']) && $data['time']['start'] != '请选择' && $data['time']['end'] != '请选择') {
            $data['start_time'] = strtotime($data['time']['start']);
            $data['end_time'] = strtotime($data['time']['end']);
        }

        return array_except($data, ['time']);
    }

    public function parseMsgType($msg_type)
    {
        return self::$msgType[$msg_type][1];
    }

    public function getMsgType($type_str)
    {
        foreach (self::$msgType as $k => $v) {
            if ($v[0] == $type_str) {
                return $k;
            }
        }
        return 0;
    }

    public function parseType($type)
    {
        return self::$type[$type][1];
    }

    public function getType($type_str)
    {
        foreach (self::$type as $k => $v) {
            if ($v[0] == $type_str) {
                return $k;
            }
        }
        return 0;
    }

    public function parseMsgContent($msg_content)
    {
        $res_josn = json_decode($msg_content);
        if($res_josn){
            if(isset($res_josn->text)){
                return $res_josn->text;
            }else{
                return $res_josn->Data.':'.$res_josn->Ext;
            }
        }
        return $msg_content;
    }

    static public function del($start, $end)
    {
        $range = [strtotime($start), strtotime($end)];
        return static::whereBetween('created_at', $range);
    }

}
