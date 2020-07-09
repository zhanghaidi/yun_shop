<?php

namespace Yunshop\Diyform\admin;

use app\common\components\BaseController;
use app\common\helpers\PaginationHelper;
use app\common\helpers\Url;
use app\common\services\wechat\WxQrCode;
use Illuminate\Support\Facades\DB;
use Yunshop\Diyform\models\DiyformOrderContentModel;
use Yunshop\Diyform\models\DiyformTypeModel;
use Yunshop\Diyform\services\DiyformService;

class DiyformController extends BaseController
{
    protected $pageSize = 10;
    public $globalData;

    public function __construct()
    {
        $DiyformService = $this->getDiyformService();
        $this->globalData = $DiyformService->globalData();
    }


    /**
     * 自定义表单管理
     */
    public function manage()
    {

        $list = DiyformTypeModel::getDiyformList()->orderBy('id', 'desc')->paginate($this->pageSize);
        $pager = PaginationHelper::show($list->total(), $list->currentPage(), $list->perPage());

        if(!$list->isEmpty()) {
            $list = $list->toArray();
        }

        return view('Yunshop\Diyform::admin.diyform-manage', [
            'list' => $list,
            'pager' => $pager
        ])->render();
    }

    //获取小程序二维码
    protected function aaa($res)
    {
        $wxqr = new WxQrCode();
        foreach ($res['data'] as $key=>$value)
        {
            $catalog = \Storage::url('app/public/qr/diyform/');

            if (!file_exists($catalog))
            {
                mkdir(base_path($catalog),777);
            }

            $file_name = \YunShop::app()->uniacid."_".$value['id'].".png";

            $real_path = str_replace('//','/',base_path($catalog).$file_name);

            if (!file_exists($real_path))
            {
                $data = array(
                    'param' => "&scene={$value['id']}",
                    'page'=> 'packageC/diyform/diyform'
                );
                $wxqr->setParam($data);
                $qrcode = $wxqr->mergeQrImage();
                if ($qrcode == false)
                {
                    \Log::debug('===生成小程序二维码获取失败====='. self::class, $data);
                    continue;
                }

                file_put_contents($real_path,$qrcode);
            }
            $res['data'][$key]['qrcode_img'] = request()->getSchemeAndHttpHost() . config('app.webPath') .$catalog. $file_name;
        }

        return $res;
    }

    /**
     * 添加表单
     */
    public function addForm()
    {
        $data_type_config = [];
        $default_data_config = [];
        $default_date_config = [];
        extract($this->globalData);
        $kw = 0;

        $data = $this->getDiyformService()->getInsertDataByAdmin();

        if ($data) {
            $insert = [
                'uniacid' => \YunShop::app()->uniacid,
                'title' => trim(\YunShop::request()->tp_title),
                'thumb' => trim(\YunShop::request()->tp_thumb),
                'description' => !empty(\YunShop::request()->tp_description) ? \YunShop::request()->tp_description : '',
                'share_description' => trim(\YunShop::request()->tp_share_description),
                'fields' => iserializer($data),
                'success' => \YunShop::request()->tp_success ? trim(\YunShop::request()->tp_success) : 0,
                'submit_number' => \YunShop::request()->tp_submit_number ? trim(\YunShop::request()->tp_submit_number) : 0,
            ];

            if (DiyformTypeModel::insert($insert)) {
                return $this->message('保存成功', Url::absoluteWeb('plugin.diyform.admin.diyform.manage'));
            } else {
                return $this->message('保存失败', '', 'error');
            }
        }
        return view('Yunshop\Diyform::admin.diyform-form', [
            'data_type_config' => $data_type_config,
            'default_data_config' => $default_data_config,
            'default_date_config' => $default_date_config,
            'kw' => $kw,
        ])->render();
    }

    /**
     * 编辑表单
     */
    public function editForm()
    {
        $id = \YunShop::request()->id;
        $diyform = DiyformTypeModel::find($id);
        $data_type_config = [];
        $default_data_config = [];
        $default_date_config = [];
        extract($this->globalData);
        $kw = 0;
        $data = $this->getDiyformService()->getInsertDataByAdmin();

        if ($data) {
            $insert = [
                'uniacid' => \YunShop::app()->uniacid,
                'title' => trim(\YunShop::request()->tp_title),
                'thumb' => trim(\YunShop::request()->tp_thumb),
                'description' => trim(\YunShop::request()->tp_description),
                'share_description' => trim(\YunShop::request()->tp_share_description),
                'fields' => iserializer($data),
                'success' => \YunShop::request()->tp_success ? trim(\YunShop::request()->tp_success) : 0,
                'submit_number' => \YunShop::request()->tp_submit_number ? trim(\YunShop::request()->tp_submit_number) : 0,
            ];

            if (DiyformTypeModel::where('id', $id)->update($insert)) {
                return $this->message('保存成功', Url::absoluteWeb('plugin.diyform.admin.diyform.manage'));
            } else {
                return $this->message('保存失败', '', 'error');
            }
        }
        return view('Yunshop\Diyform::admin.diyform-form', [
            'item' => $diyform,
            'dfields' => iunserializer($diyform['fields']),
            'data_type_config' => $data_type_config,
            'default_data_config' => $default_data_config,
            'default_date_config' => $default_date_config,
            'kw' => $kw,
        ])->render();

    }

    /**
     * 删除表单
     */
    public function delForm()
    {
        $id = \YunShop::request()->id;
        $diyform = DiyformTypeModel::find($id);

        if (!$diyform) {
            return $this->message('无此自定义表单或已经删除', '', 'error');
        }

        $result = DiyformTypeModel::where('id', $id)->delete();
        if ($result) {
            return $this->message('删除自定义表单成功', Url::absoluteWeb('plugin.diyform.admin.diyform.manage'));
        } else {
            return $this->message('删除自定义表单失败', '', 'error');
        }

    }

    public function getDiyformService()
    {
        return (new DiyformService());
    }

    public function getDataByOrder($order_id)
    {
        $order = DiyformOrderContentModel::uniacid()->where('order_id',$order_id)->orderBy('id','desc')->first();
        return view('Yunshop\Diyform::order.order',[
            'order_id'=>$order_id,
            'order'=>$order
        ])->render();
    }

    public function getFormDataByOderId($order_id)
    {
        $order_id = \YunShop::request()->get('order_id');
        $order = DiyformOrderContentModel::uniacid()
            ->where('order_id',$order_id)
            ->select(['id','data', 'created_at','goods_id',DB::raw('MAX(id) as ids')])
            ->groupBy('goods_id')
            ->get();
        $result = DiyformOrderContentModel::uniacid()
            ->where('order_id',$order_id)
            ->groupBy('goods_id')
            ->with('form')
            ->get();
        foreach($result as $key=>&$value){
            $value['form']['fields1'] = unserialize($value['form']['fields']);
        }
        foreach($order as $key=>&$value){
            $value['data'] = unserialize($value['data']);
            $value['form'] = $result[$key]['form'];
        }

        return view('Yunshop\Diyform::order.detail',[
            'data'=>$order,
            'id'=>$order_id
        ])->render();
    }



}