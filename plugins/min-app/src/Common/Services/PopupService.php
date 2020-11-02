<?php

namespace Yunshop\MinApp\Common\Services;

use Yunshop\MinApp\Common\Models\Popup;
use Yunshop\MinApp\Common\Models\PopupPositon;
use app\common\Facades\Setting;
use Illuminate\Support\Facades\DB;

class PopupService {

    protected $popup;
    protected $popupPosition;
    protected static $appKey = ['main'=>'key','shop'=>'shop_key'];

    public static function getPopup(){
        $request = request();
        $app_type = $request->app_type ? $request->app_type : 'main';
        $app_key = isset($app_key[$app_type]) ? self::$appKey[$app_type] : self::$appKey['main'];
        $applet_set = Setting::get('plugin.min_app');
        $appid = $applet_set[$app_key];
        $weapp_account = DB::table('account_wxapp')->where('key','=',$appid)->orderBy('acid','desc')->get()->toArray();
        $position = PopupPositon::uniacid()->where([['type','=',intval($request->pop_type)],['is_show','=',1]])->whereIn('weapp_account_id',array_column($weapp_account,'acid'))->first();
        //DB::enableQueryLog();
        //DB::getQueryLog();
        if($position->id){
            $popup = Popup::uniacid()->where([['position_id','=',$position->id],['is_show','=',1],['start_time','<=',time()],['end_time','>=',time()]])->orderBy('sort','desc')->first();
            if($popup){
                return [[
                    'id' => $popup['id'],
                    'picture' => yz_tomedia($popup['picture']),
                    'page_path' => $popup['pagepath'],
                    'web_link' => $popup['web_link'],
                ]];
            }
        }
        return [];
    }

}