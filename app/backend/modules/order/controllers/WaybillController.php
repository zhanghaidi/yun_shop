<?php

namespace app\backend\modules\order\controllers;


use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use Ixudra\Curl\Facades\Curl;
use app\common\models\Order;
use app\common\models\Goods;
use app\frontend\modules\order\services\OrderService;
use Illuminate\Support\Facades\Input;
use Yunshop\Exhelper\common\models\ElectronicTemplate;
use Yunshop\Exhelper\common\models\ExhelperPanel;
use Yunshop\Exhelper\common\models\ExhelperSys;
use Yunshop\Exhelper\common\models\SendUser;
use app\common\models\member\Address;
// use Ixudra\Curl\Facades\Curl;

class WaybillController extends BaseController
{
	protected $apikey;
	protected $merchant_id;
	protected $name;
	protected $print_url = 'http://www.kdniao.com/External/PrintOrder.aspx';
	// 测试地址 'http://testapi.kdniao.com:8081/api/Eorderservice';
	protected $test_panel_url = 'http://testapi.kdniao.com:8081/api/Eorderservice';
    // 正式地址  'http://api.kdniao.com/api/Eorderservice';
	protected $panel_url = 'http://api.kdniao.com/api/eorderservice';
    protected $zip_code;
	public $company = [ 
		'SF' => '顺丰速运',
		'EMS' => 'EMS',
		'ZJS' => '宅急送',
		'YTO' => '圆通速递',
		'HTKY' => '百世快递',
		'ZTO' => '中通快递',
		'YD' => '韵达速递',
		'STO' => '申通快递',
		'DBL' => '德邦快递',
		'UC' => '优速快递',
		'JD' => '京东快递',
		'XFEX' => '信丰物流',
		'ANE' => '安能物流',	
		'GTO' => '国通快递',
		'HHTT' => '天天快递',
		'KYSY' => '跨越速运',
		'YZPY' => '邮政快递包裹',
		'ZTKY' => '中铁快运',
		'YZBK' => '邮政国内标快',
		'YCWL' => '远成快运',
		'UAPEX' => '全一快递',
		'SURE' => '速尔快递',
		'PJ' => '品骏快递',
		'DBLKY' => '德邦快运',
		'ANEKY' => '安能快运',
		'JDKY' => '京东快运',
		'LB' => '龙邦快递',
		'CND' => '承诺达',
		'HTKYKY' => '百世快运',
		'ZTOKY' => '中通快运',
        'SX'    => '顺心捷达',
        'JTSD'    => '极兔速递'
	];

	public $province = [
		'北京' => 11,
		'天津' => 12,
		'河北省' => 13,
		'山西省' => 14,
		'内蒙古自治区' => 15,
		'辽宁省' => 21,
		'吉林省' => 22,
		'黑龙江省' => 23,
		'上海' => 31,
		'江苏省' => 32,
		'浙江省' => 33,
		'安徽省' => 34,
		'福建省' => 35,
		'江西省' => 36,
		'山东省' => 37,
		'河南省' => 41,
		'湖北省' => 42,
		'湖南省' => 43,
		'广东省' => 44,
		'广西壮族自治区' => 45,
		'海南省' => 46,
		'重庆' => 50,
		'四川省' => 51,
		'贵州省' => 52,
		'云南省' => 53,
		'西藏自治区' => 54,
		'陕西省' => 61,
		'甘肃省' => 62,
		'青海省' => 63,
		'宁夏回族自治区' => 64,
		'新疆维吾尔自治区' => 65,
		'台湾' => 71,
		'香港特别行政区'  => 81, 
		'澳门特别行政区' => 82,
	];

	public function __construct()
	{
    	$set = ExhelperSys::uniacid()->first();
    	if ($set) {
    		$this->name = trim($set['name']);
    		$this->apikey = trim($set['apikey']);
    		$this->merchant_id = trim($set['merchant_id']);
    	}
//    	else {
//    		return $this->message('请配置面单', Url::absoluteWeb('plugin.exhelper.admin.panel.index'));
//    	}
	}



    public function waybill()
    {
    	$ordersn = request()->id;
//        $this->zip_code = request()->zip_code;

        if (!app('plugins')->isEnabled('exhelper')){
            return json_encode(array('result'=>'error','resp'=>'插件没有开启'));
        }
        $order_model = Order::where('id', $ordersn)->first();

    	if (!$ordersn || !$order_model) {
    		return json_encode(array('result'=>'error','resp'=>'请传参数订单编号ordersn'));
    	}
    	
    	if (count($ordersn) > 1) {
    		return json_encode(array('result'=>'error', 'resp'=>'订单超过规定数量,请选择1条订单'));
    	}

        $template_model = ElectronicTemplate::uniacid()->where('order_sn',$order_model['order_sn'])->first();

        if ($template_model){
            $expressCompanies = \app\common\repositories\ExpressCompany::create()->all();
            $company = '';
            array_filter( $expressCompanies, function($var) use($template_model,&$company){
                if(in_array($template_model['shipper_code'],$var)){
                    $company = $var;
                    return $var;
                }
            });
            return json_encode(array('result'=>'success', 'resp'=>['logistic_code'=>$template_model['logistic_code'],
                'shipper_code'=>$template_model['shipper_code'],'company'=> $company]));
        }
    	
    	if (!is_array($ordersn) || count($ordersn) == 1) {
    		// $order_sn = explode(',', $ordersn);
    		$order = Order::where('id', $ordersn)
    	               ->uniacid()
    				   ->with('hasManyOrderGoods')
                       ->with('hasManyOrderGoods.goods')
    	               ->with('hasOneDispatchType')
    	               ->with('belongsToMember')
    	               ->with('address')
    	               ->first()
    	               // ->find($id)
    	               ->toArray();
//    	   dd($order['has_many_order_goods']);
    		return $this->beginHandle($order);
    	}
    }

   /**
    * 快递鸟建议用户使用时，详细查看以下注意事项：
	* 1、顺丰速运、中铁快运、宅急送、全一快递可以不申请电子面单客户号，直接下单（如果用户已和网点申请到电子面单客户号，严格按照表格对应关系填写）；
	* 3、顺丰速运，运费现付、到付发货，无需客户号，运费如需月结，仅需要联系网点申请月结号（10位数的纯数字格式）；
	* 4、通过快递鸟用户管理后台申请的优速电子面单客户号，只需要客户编号即可正常使用电子面单接口，通过当地快递网点线下申请的需要使用客户编号和密钥才可下单；
	* 5、通过快递鸟用户管理后台申请的中通电子面单客户号，只需要商家ID即可正常使用电子面单接口，通过当地快递网点线下申请的需要使用商家ID和商家接口密码才可下单；
	* 6、宅急送可直接请求电子面单接口下单使用，不用申请电子面单客户号，如果用户是自己联系当地网点申请电子面单客户号，那么LogisticCode（快递单号）字段为必填（网点会提供）；
	* 7、速尔快递，必须传值SendStaff收件快递员，否则直接影响网点收件；
	* 8、安能快递，仅支持通过快递鸟后台申请的客户号发货，客户号格式如1111111_ANE666，网点名称格式如6666666.
    * 京东快运下单，需联系京东站点维护事业部编码(EBU开头)、仓库编码以及寄件人简称。
    * 宅急送、速尔快递、远成快运、品骏快递、快运公司无测试环境  
    * 
    */
    public function beginHandle($order)
    {
    	// 正式地址： http://api.kdniao.com/api/eorderservice（不加密）https://api.kdniao.com/api/eorderservice（加密）
    	$add = explode(' ', $order['address']['address']);
    	\Log::info('-=-=add=-=-', $add);
    	$send = SendUser::uniacid()->where('isdefault', 1)->first();
    	\Log::info('-=-=send=-=-', $send);
    	
    	if (empty($send)) {
    		//查找寄件人信息表
            die(json_encode(array('result' => 'error', 'resp' => '寄件人信息为空,请输入后在选择。')));
//    		return $this->message('寄件人信息为空,请输入后在选择', Url::absoluteWeb('plugin.exhelper.admin.panel.index'));
    	}
        $send = $send->toArray();
    	$panel = ExhelperPanel::uniacid()->where('isdefault', 1)->first();
    	// dd($panel->exhelper_style);
    	
    	\Log::info('panel--uniacid', [$panel, \Yunshop::app()->uniacid]);
    	
    	if (empty($panel)) {
            die(json_encode(array('result' => 'error', 'resp' => '无默认模板信息')));
//			return $this->message('无默认模板信息', Url::absoluteWeb('plugin.exhelper.admin.panel.index'));
    	}
    	\Log::info('begin---panel');

    	$sender = array(
	    	'Name' => $send['sender_name'],
			'Mobile' => $send['sender_tel'],
			'ProvinceName' => $send['sender_province'],
			'CityName' => $send['sender_city'],
			'ExpAreaName' => $send['sender_area'],
			'Address' => $send['sender_street'].$send['sender_address']
    	);

		$receiver = array(
			'Name' => $order['address']['realname'],
			'Mobile' => $order['address']['mobile'],
			'ProvinceName' => $add[0],
			'CityName' => $add[1],
			'ExpAreaName' => $add[2],
			'Address' => $add[3].$add[4]
		);
		if ($receiver['Address'] == ''){
            $receiver['Address'] = $add[3].$add[4].$add[5];
        }
		if (in_array($panel['exhelper_style'], ['EMS','YZPY','YZBK'])) {
			//为邮政时需要获取邮政编码
			if ($this->getCode($send['sender_province'],$send['sender_city'], $send['sender_area']) != 1) {

				$sender['PostCode'] = $sender['sender_code']  ?  : $this->getCode($send['sender_province'],$send['sender_city'], $send['sender_area']);
			}

//			if (empty($this->zip_code)){
//                die(json_encode(array('result' => 'error', 'resp' => '邮政编码不能为空')));
//            }else{
//                $receiver['PostCode'] = $this->zip_code;
//            }
			if ($this->getCode($add[0],$add[1], $add[2]) != 1) {

				$receiver['PostCode'] = $this->getCode($add[0],$add[1], $add[2]);
			}
		}
		// dd($sender, $receiver);

		$commodity = [];
		$weight = '';

		foreach ($order['has_many_order_goods'] as $k => $v) {
			$commodityOne = [
				//商品名称
				'GoodsName' => $v['title'],
				//商品件数
				'Goodsquantity' => $v['total'],
				//商品价格
				'GoodsPrice' => $v['goods_price'],
				//商品编码
				'GoodsCode' => $v['goods_sn'],
				//商品描述
				// 'GoodsDesc' => $v['goods_option_title'],
				//商品重量
				'GoodsWeight' => $v['goods']['weight'] ? $v['goods']['weight'] * $v['total'] : '',
				//商品体积
				'GoodsVol' => ''
			];
            $weight += $v['goods']['weight'] ? $v['goods']['weight'] * $v['total'] : 0;
			$commodity[] = $commodityOne;
		}
//		 dd($commodity,$weight);
		$PayType = $panel['panel_name'] == '德邦快递' ? 3 : 1;
		$data = [
			'Sender' => $sender,
    		'Receiver' => $receiver,
    		//商品信息
			'Commodity' => $commodity,
			// 包裹总重量 (kg) double(10,3)
			'Weight' =>  $weight ?: '',
			// 运输方式 1陆运 2空运 默认为1
			'TransType' => 1,
			//快递公司编码
    		'ShipperCode' => $panel['exhelper_style'],
    		//第三方订单号 物流为京东且 ExpType =1时必填
    		'ThrOrderCode' => $order['order_sn'],
    		//订单编号
    		'OrderCode' => $order['order_sn'],//$order['order_sn'].rand(1111, 9999),
    		//运费支付方式 1现付 2到付 3月结 4第三方付
    		// 'PayType' => $order['pay_type_id'],
    		'PayType' => $PayType,
    		// 快递公司业务类型
    		'ExpType' => 1,
    		//是否要求签回单 0不要求 1要求
    		'IsReturnSignBill' => 0,
    		//快递运费
    		'Cost' => '',
    		//其他费用
    		'OtherCost' => '',
    		//是否通知快递员上门揽件
			'IsNotice' => $panel->isself,
			//包裹数
			'Quantity' => count($order['has_many_order_goods']),
			//包裹为快运时必填，包裹总体积
			'Volume' => '',
			// 'Remark' => $order['remark'],
			'Remark' => $order['note'],
			//返回电子面单模板
			'IsReturnPrintTemplate' => 1,
			//送货方式 0自提 1送货上门 2送货上楼
			'DeliveryMethod' => 1
		];

		/*
		 * JD
		 * 快递鸟用户，通常ExpType传值6.
		 * 订单来源：京东商城 1
		 * 订单来源：天猫 2
		 * 订单来源：苏宁 3
		 * 订单来源：亚马逊中国 4
		 * 订单来源：ChinaSkin 5
		 * 订单来源：其他销售平台 6
		 */
        if ($panel['exhelper_style'] == 'JD'){
            $data['ExpType'] = 6;
        }
        if ($panel['exhelper_style'] == 'SF'){
            $data['TemplateSize'] = 15001;
        }
		// dd($data);
		// dd($panel->isself);
		if ($panel->isself == 1) {
			//上门揽件时间
			$data['StartDate'] = $panel->begin_time;
			$data['EndDate'] = $panel->end_time;
		} 

		$set = $this->panelSet();

    	if (in_array($panel['exhelper_style'], ['DBL','PJ','DBLKY'])) {
		   		
    		$data['CustomerName'] = trim($panel['panel_sign']);

    	} elseif (!in_array($panel['exhelper_style'], ['SF','ZTKY', 'UAPEX', 'HOAU']) || ($panel['exhelper_style'] == 'EMS' && $sender['ProvinceName'] != '广东省')) {
    		
    		$data['CustomerName'] = trim($panel['panel_no']);
    		
    	} else {
    		$data['CustomerName'] = '';

    	}
    	$data['CustomerPwd'] = $set[$panel['exhelper_style']]['CustomerPwd']  ? trim($panel['panel_pass']) : '';
    	$data['MonthCode'] = ($set[$panel['exhelper_style']]['MonthCode'] && $panel['exhelper_style'] == 'YTO') ? trim($panel['panel_pass']) : '';
    	$data['SendSite'] = $set[$panel['exhelper_style']]['SendSite'] ? trim($panel['panel_code']) : '';
    	//快递公司参数 SendStaff-速尔快递需要提供收件快递员信息，LogisticCode-宅急送需要快递单号信息-有待完善
    	$data['SendStaff'] = (isset($set[$panel['exhelper_style']]['SendStaff']) && $panel['exhelper_style'] == 'SURE') ? '' : '';
    	$data['LogisticCode'] = (isset($set[$panel['exhelper_style']]['LogisticCode']) && $panel['exhelper_style'] == 'ZJS') ? '' : '' ;

    	//快运公司参数 京东快运需要仓库编码和寄件人简称
    	$data['WareHouseID'] = (isset($set[$panel['exhelper_style']]['WareHouseID']) && $panel['exhelper_style'] == 'JDKY') ? trim($panel['panel_code']) : '' ;
    	$data['Name'] = (isset($set[$panel['exhelper_style']]['Name']) && $panel['exhelper_style'] == 'JDKY')?  trim($send['sender_name']) : '';
    	// dd($data);
   		// header('Content_Type', 'Content-Type:application/json');

    	//return $this->getCompanyPanel($data, $this->test_panel_url, $order['id']); //测试地址
    	return $this->getCompanyPanel($data, $this->panel_url, $order['id'],$order['order_sn']); //正式地址
    }

    //获取快递公司电子面单
    public function getCompanyPanel($data, $url, $order_id,$order_sn)
    {
    	\Log::info('get---panel');

		//调用电子面单
		$jsonParam = json_encode($data, JSON_UNESCAPED_UNICODE);

		$jsonResult = $this->submitEOrder($jsonParam, $url);
    	\Log::info('data2', $data);

		//解析电子面单返回结果
		$result = json_decode($jsonResult, true);
    	\Log::info('result', $result);

		if($result['ResultCode'] == '100' && $result['Success'] == 'true') {
			
			$panelsetting = ExhelperPanel::uniacid()->where('isdefault',1)->first()->toArray();
			
			$opera = new OrderService;

			$param = array(
					'order_id' => $order_id,
					'express_code' => $result['Order']['ShipperCode'], 
					'express_company_name' => $this->company[$result['Order']['ShipperCode']],
					'express_sn' => $result['Order']['LogisticCode']
			);
//            dd($result);
			// $opera->orderSend($param);
            if (!$this->saveTemplate($result,$order_sn)){

                return json_encode(array('result'=>'error','resp'=>'电子面单储存失败'));
            }
            $expressCompanies = \app\common\repositories\ExpressCompany::create()->all();
            $company = '';
            array_filter( $expressCompanies, function($var) use($result,&$company){
                if(in_array($result['Order']['ShipperCode'],$var)){
                    $company = $var;
                    return $var;
                }
            });
			return json_encode(array('result'=>'success', 'resp'=>['logistic_code'=>$result['Order']['LogisticCode'], 'shipper_code'=>$result['Order']['ShipperCode'],
                'company'=> $company]));
		
		} else {
			return json_encode(array('result'=>'error', 'resp'=>$result['Reason']));
		}

    }

    /*
     * 电子面单打印优化，储存电子面单模板
     */
    public function saveTemplate($result,$order_sn)
    {
        if (!$order_sn){
            return false;
        }
        if (!$result){
            return false;
        }
        $data = [
            'uniacid'   => \Yunshop::app()->uniacid,
            'order_sn'  => $order_sn,
            'print_template'    => $result['PrintTemplate'],
            'mark_destination'  => $result['Order']['MarkDestination'] ?: '',
            'logistic_code'     => $result['Order']['LogisticCode'] ?: '',
            'shipper_code'     => $result['Order']['ShipperCode'] ?: '',
            'order_code'       => $result['Order']['OrderCode'] ?: '',
            'kdn_order_code'     => $result['Order']['KDNOrderCode'] ?: '',
            'package_code'     => $result['Order']['PackageCode'] ?: '',
            'sorting_code'     => $result['Order']['SortingCode'] ?: '',
            'sub_count'     => $result['SubCount'] ?: '',
            'ebusiness_id'     => $result['EBusinessID'] ?: '',
            'uniquer_request_number'     => $result['UniquerRequestNumber'] ?: '',
            'result_code'     => $result['ResultCode'],
            'reason'     => $result['Reason'],
            'success'     => $result['Success']
        ];

        $template_model = new ElectronicTemplate();

        $template_model->setRawAttributes($data);

        $validator = $template_model->validator($template_model->getAttributes());

        if ($validator->fails()) {
            $this->error($validator->messages());
        } else {

            if ($template_model->save()) {
                //显示信息并跳转
                return true;
            } else {
                return false;
            }
        }

    }

    private function submitEOrder($requestData, $url) 
    {
		$datas = array(
	        'EBusinessID' => $this->merchant_id,
	        'RequestType' => '1007',
	        'RequestData' => urlencode($requestData),
	        'DataType' => '2',
	    );
	    $datas['DataSign'] = $this->encrypt($requestData, $this->apikey);

        $result = Curl::to($url)->withData($datas)->post();
		
		return $result;
	}

	/**
	 * 电商Sign签名生成
	 * @param data 内容   
	 * @param appkey Appkey
	 * @return DataSign签名
	 */
	private function encrypt($data, $appkey) {
	    return urlencode(base64_encode(md5($data.$appkey)));
	}


	/**
	 * 获取地址区域邮政编码
	 * @param string $provice_name 省份名称
	 * @param string $area_name 区/县名称
	 * @return int $code 邮政编码
	 */
	public function getCode($provice_name,$city_name, $area_name)
	{
	    $address = new Address();

        $provice_id = $address->where('areaname','like','%'.$city_name.'%')->first();
		$area_id = $address->where('areaname', 'like','%'.$area_name.'%')->where('parentid',$provice_id->id)->select('id')->first();
		if(!$area_id) {
			\Log::info('通过数据表获取城市名'.$city_name.'失败');
			return 1;
		}

		$area_id = $area_id->toArray();

		$url = 'http://www.ems.com.cn/ems/tool/rpq/queryPostCode?city='.$area_id['id'].'&province='.$this->province[$provice_name];
		
		$code = json_decode(Curl::to($url)->get(), true);

		if (is_array($code['model']['postCode'])) {
			return $code['model']['postCode'][0];
		}
		\Log::info('====--请求获取邮政编码失败, 地址信息为'.$provice_name.$city_name);
		return 1;
	}

	//快递公司参数配置
    public function panelSet()
    {
        return [
        	'EMS' => [
        		'CustomerName' => '大客户号',
        		'CustomerPwd'  => 'APP_SECRET',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'SF' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '月结号(选填)',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'ZTKY' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'YZBK' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'YZPY' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'ZJS' => [
        		'CustomerName' => '标识',
        		'CustomerPwd'  => '秘钥',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => '快递单号',
        	],
        	'UAPEX' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'ZTO' => [
        		'CustomerName' => '商家ID',
        		'CustomerPwd'  => '商家接口密码(选填)',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'STO' => [
        		'CustomerName' => '客户简称',
        		'CustomerPwd'  => '客户密码',
        		'MonthCode'    => '',
        		'SendSite'     => '所属网点',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'DBL' => [
        		'CustomerName' => '月结编码',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'JD' => [
        		'CustomerName' => '商家编码',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'XFEX' => [
        		'CustomerName' => '客户平台ID',
        		'CustomerPwd'  => '客户平台验证码',
        		'MonthCode'    => '',
        		'SendSite'     => '客户商号ID或仓库ID',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'HHTT' => [
        		'CustomerName' => '客户帐号',
        		'CustomerPwd'  => '客户密码',
        		'MonthCode'    => '',
        		'SendSite'     => '网点名称',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'GTO' => [
        		'CustomerName' => '客户简称',
        		'CustomerPwd'  => '客户密码',
        		'MonthCode'    => '',
        		'SendSite'     => '网点名称',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'SURE' => [
        		'CustomerName' => '客户号',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '网点编号(仓库号)',
        		'SendStaff'    => '收件快递员(网点提供)',
        		'LogisticCode' => ''
        	],
        	'KYSY' => [
        		'CustomerName' => '客户号',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'YD' => [
        		'CustomerName' => '客户ID',
        		'CustomerPwd'  => '接口联调密码',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'HTKY' => [
        		'CustomerName' => '操作编码',
        		'CustomerPwd'  => 'ERP秘钥',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'YTO' => [
        		'CustomerName' => '商家代码',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '密钥串',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'YCWL' => [
        		'CustomerName' => '商户代码',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '网点名称',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'UC' => [
        		'CustomerName' => '客户编号',
        		'CustomerPwd'  => '密钥(选填)',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'ANE' => [
        		'CustomerName' => '客户号',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '网点名称(仅数字部分)',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'PJ' => [
        		'CustomerName' => '月结号',
        		'CustomerPwd'  => '密钥',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'CND' => [
        		'CustomerName' => '客户编码',
        		'CustomerPwd'  => '客户密码',
        		'MonthCode'    => '',
        		'SendSite'     => '网点名称',
        		'SendStaff'    => '',
        		'LogisticCode' => ''
        	],
        	'HOAU' => [
        		'CustomerName' => '',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '',
        		'Name' => ''
        	],
        	'DBLKY' => [
        		'CustomerName' => '月结编码',
        		'CustomerPwd'  => '',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '',
        		'Name' => ''
        	],
        	'ANEKY' => [
        		'CustomerName' => '客户编码',
        		'CustomerPwd'  => '客户秘钥',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '',
        		'Name' => ''
        	],
        	'JDKY' => [
        		'CustomerName' => '商家编码',
        		'CustomerPwd'  => '事业部编码(EBU开头)',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '仓库编码',
        		'Name' => '寄件人简称'
        	],
        	'LB' => [
        		'CustomerName' => '电子面单账号',
        		'CustomerPwd'  => '密码',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '',
        		'Name' => ''
        	],
        	'HTKYKY' => [
        		'CustomerName' => '账户信息',
        		'CustomerPwd'  => '账户密码',
        		'MonthCode'    => '',
        		'SendSite'     => '',
        		'WareHouseID'    => '',
        		'Name' => ''
        	],
        	'ZTOKY' => [
        		'CustomerName' => '商家CP',
        		'CustomerPwd'  => '商家秘钥',
        		'MonthCode'    => '',
        		'SendSite'     => '寄件网点ID',
        		'WareHouseID'    => '',
        		'Name' => ''
        	]
        ];
    }

    //批量打印
    /**
	 * 组装POST表单用于调用快递鸟批量打印接口页面
	 */
	public function build_form() {
		//OrderCode:需要打印的订单号，和调用快递鸟电子面单的订单号一致，PortName：本地打印机名称，请参考使用手册设置打印机名称。支持多打印机同时打印。
		// $request_data = '[{"OrderCode":"234351215333113311353","PortName":"打印机名称一"},{"OrderCode":"234351215333113311354","PortName":"打印机名称二"}]';
		$ordersns = request()->ordersn;
		// dd($ordersns);
		foreach ($ordersns as $k => $v) {
			$request_data[$k]['OrderCode'] = $v;
			$request_data[$k]['PortName'] = $this->name;
		}
		\Log::info('--panel_print---ordersn,--request-data', ['ordersn'=>$ordersns, 'request_data'=>$request_data]);

		$request_data_encode = urlencode($request_data);
		$data_sign = $this->encrypt($this->get_ip().$request_data_encode, $this->apikey);
		//是否预览，0-不预览 1-预览
		$is_priview = '0';

		//组装表单
		$form = '<form id="form1" method="POST" action="'.$this->print_url.'"><input type="text" name="RequestData" value="'.$request_data.'"/><input type="text" name="EBusinessID" value="'.$this->merchant_id.'"/><input type="text" name="DataSign" value="'.$data_sign.'"/><input type="text" name="IsPriview" value="'.$is_priview.'"/></form><script>form1.submit();</script>';
		print_r($form);
	}

	/**
	 * 判断是否为内网IP
	 * @param ip IP
	 * @return 是否内网IP
	 */
	private function is_private_ip($ip) {
	    return !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	/**
	 * 获取客户端IP(非用户服务器IP)
	 * @return 客户端IP
	 */
	private function get_ip() {
		//获取客户端IP
		if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
	        $ip = getenv('HTTP_CLIENT_IP');
	    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
	        $ip = getenv('HTTP_X_FORWARDED_FOR');
	    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
	        $ip = getenv('REMOTE_ADDR');
	    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }

		if(!$ip || $this->is_private_ip($ip)) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'http://www.kdniao.com/External/GetIp.aspx');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			return $output;
		}
		else{
			return $res;
		}
	}

}