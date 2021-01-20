<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com YangYu
 * Date: 2018/12/4
 * Time: 下午4:40
 */

namespace Yunshop\Tbk\admin;



use app\common\components\BaseController;
use Yunshop\Tbk\common\jobs\CatchGoodsJob;
use Yunshop\Tbk\common\services\TaobaoService;

class SelectionController  extends BaseController
{
    private $taobao = null;

    public function __construct()
    {
        $set = \Setting::get('plugin.tbk');
        $this->taobao = new TaobaoService($set['appkey'], $set['secret'], $set['ad_zone_id']);
        //$this->taobao = new TaobaoService('25309431', 'f2b4f813e30ef8d1da3d4690392f71bd', '64711400468');
    }

    public function index() {
        echo "11";
    }

    public function test()
    {
        $this->taobao->getGoodsDetail();
    }

    public function coupon() {
        $this->taobao->searchCoupon('纸巾');
    }

    public function favourite() {
        $id = \YunShop::request()->id;
        dispatch(new CatchGoodsJob($id, \YunShop::app()->uniacid));
        return $this->successJson();
    }

    public function testfav()
    {
        $set = \Setting::get('plugin.tbk');
        //$taobao = new TaobaoService($set['appkey'], $set['secret'], $set['ad_zone_id']);
        $this->taobao->favourite('19070636');
    }

    public function favList() {

        $favlist = $this->taobao->getFavouriteList();

        return view('Yunshop\Tbk::admin.favlist', [
            'favlist' => $favlist
        ])->render();
    }

    public function getCode() {
        /*测试时，需把test参数换成自己应用对应的值*/
        $url = 'https://oauth.taobao.com/authorize';
        $postfields= array('grant_type'=>'authorization_code',
            'client_id'=>'25309431',
            'response_type'=>'code',
            'redirect_uri'=>'https://dev2.yunzshop.com');
        $post_data = '';
        foreach($postfields as $key=>$value){
            $post_data .="$key=".urlencode($value)."&";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        //指定post数据
        curl_setopt($ch, CURLOPT_POST, true);
        //添加变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, substr($post_data,0,-1));
        $output = curl_exec($ch);

        curl_close($ch);
        var_dump($output);

    }
}