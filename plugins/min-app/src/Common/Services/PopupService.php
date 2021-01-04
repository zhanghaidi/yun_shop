<?php

namespace Yunshop\MinApp\Common\Services;

use app\common\models\user\User;
use Yunshop\MinApp\Common\Models\Popup;
use Yunshop\MinApp\Common\Models\PopupPositon;
use app\common\Facades\Setting;
use Illuminate\Support\Facades\DB;
use app\common\models\OperationLog;


class PopupService {

    protected $popup;
    protected $popupPosition;
    protected static $appKey = ['main'=>'key','shop'=>'shop_key'];
    protected $logs;

    public static function getPopup(){
        $request = request();
        $app_type = $request->app_type ? $request->app_type : 'main';
        $app_key = isset($app_key[$app_type]) ? self::$appKey[$app_type] : self::$appKey['main'];
        $applet_set = Setting::get('plugin.min_app');
        $appid = $applet_set[$app_key];
        $weapp_account = DB::table('account_wxapp')->where('key','=',$appid)->orderBy('acid','desc')->get()->toArray();
        $position = PopupPositon::uniacid()->where([['type','=',intval($request->pop_type)],['is_show','=',1]])->whereIn('weapp_account_id',array_column($weapp_account,'acid'))->first();
        if($position->id){
            $popup = Popup::uniacid()->where([['position_id','=',$position->id],['is_show','=',1],['start_time','<=',time()],['end_time','>=',time()]])->orderBy('sort','desc')->first();
            if($popup){
                return [[
                    'id' => $popup['id'],
                    'picture' => yz_tomedia($popup['picture']),
                    'page_path' => $popup['pagepath'],
                    'web_link' => $popup['web_link'],
                    'show_time' => $popup['show_time'] > 0 ? $popup['show_time'] : 5,
                ]];
            }
        }
        return [];
    }

    public function logPopup(Popup $_model,$type){
        $this->setDefault();
        $this->setLog('type', $type);
        $this->setLog('field', 'id');
        $this->setLog('field_name', '弹窗ID');
        $this->setLog('old_content', '小程序弹窗编辑');
        $this->setLog('new_content', $_model->id);
        $this->setLog('mark', $_model->id);

        OperationLog::create($this->logs);
    }

    public function logPopupPosition(PopupPositon $_model,$type){
        $this->setDefault();
        $this->setLog('type', $type);
        $this->setLog('field', 'id');
        $this->setLog('field_name', '弹窗位置ID');
        $this->setLog('old_content','小程序弹窗位置编辑');
        $this->setLog('new_content', $_model->id);
        $this->setLog('mark', $_model->id);

        OperationLog::create($this->logs);
    }
    
    public function setLog($log, $logValue = '')
    {
        $this->logs[$log] = $logValue ? $logValue :'';
    }    
    
    public function setDefault()
    {
        $uid = intval(\YunShop::app()->uid);
        $user_name = User::where('uid', $uid)->value('username');
        if ($user_name) {
            $this->setLog('user_name', $user_name);
        }
        $this->setLog('user_id', $uid);
        $this->logs['uniacid'] = \YunShop::app()->uniacid;
        $this->logs['ip']      = $_SERVER['REMOTE_ADDR'];
        $this->logs['modules'] = 'shop';
    }
}